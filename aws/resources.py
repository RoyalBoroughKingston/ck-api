from troposphere import GetAtt, Ref, Base64, Join, Sub, Select
import troposphere.autoscaling as autoscaling
import troposphere.awslambda as awslambda
from troposphere.cloudformation import AWSCustomObject
import troposphere.ec2 as ec2
import troposphere.ecr as ecr
import troposphere.ecs as ecs
import troposphere.elasticache as elasticache
import troposphere.elasticloadbalancingv2 as elb
import troposphere.elasticsearch as elasticsearch
import troposphere.iam as iam
import troposphere.logs as logs
import troposphere.rds as rds
import troposphere.s3 as s3
import troposphere.sqs as sqs


class CustomLogGroupPolicy(AWSCustomObject):
    resource_type = "Custom::LogGroupPolicy"
    props = {
        "ServiceToken": (str, True),
        "CWLOGS_NAME": (str, True),
        "CWLOGS_ARN": (str, True)
    }


def create_load_balancer_security_group_resource(template):
    return template.add_resource(
        ec2.SecurityGroup(
            'LoadBalancerSecurityGroup',
            GroupDescription='For connecting to the API load balancer',
            SecurityGroupIngress=[
                ec2.SecurityGroupRule(
                    Description='HTTP access from the public',
                    IpProtocol='tcp',
                    FromPort='80',
                    ToPort='80',
                    CidrIp='0.0.0.0/0'
                ),
                ec2.SecurityGroupRule(
                    Description='HTTPS access from the public',
                    IpProtocol='tcp',
                    FromPort='443',
                    ToPort='443',
                    CidrIp='0.0.0.0/0'
                )
            ]
        )
    )


def create_api_security_group_resource(template, load_balancer_security_group_resource):
    return template.add_resource(
        ec2.SecurityGroup(
            'ApiSecurityGroup',
            GroupDescription='For connecting to the API containers',
            SecurityGroupIngress=[
                ec2.SecurityGroupRule(
                    Description='Full access from the load balancer',
                    IpProtocol='tcp',
                    FromPort='0',
                    ToPort='65535',
                    SourceSecurityGroupName=Ref(
                        load_balancer_security_group_resource)
                )
            ]
        )
    )


def create_database_security_group_resource(template, api_security_group_resource):
    return template.add_resource(
        ec2.SecurityGroup(
            'DatabaseSecurityGroup',
            GroupDescription='For connecting to the MySQL instance',
            SecurityGroupIngress=[
                ec2.SecurityGroupRule(
                    Description='MySQL access from the API containers',
                    IpProtocol='tcp',
                    FromPort='3306',
                    ToPort='3306',
                    SourceSecurityGroupName=Ref(api_security_group_resource)
                )
            ]
        )
    )


def create_redis_security_group_resource(template, api_security_group_resource):
    return template.add_resource(
        ec2.SecurityGroup(
            'RedisSecurityGroup',
            GroupDescription='For connecting to the Redis cluster',
            SecurityGroupIngress=[
                ec2.SecurityGroupRule(
                    Description='Redis access from the API containers',
                    IpProtocol='tcp',
                    FromPort='6379',
                    ToPort='6379',
                    SourceSecurityGroupName=Ref(api_security_group_resource)
                )
            ]
        )
    )


def create_database_subnet_group_resource(template, subnets_parameter):
    return template.add_resource(
        rds.DBSubnetGroup(
            'DatabaseSubnetGroup',
            DBSubnetGroupDescription='Subnets available for the RDS instance',
            SubnetIds=Ref(subnets_parameter)
        )
    )


def create_database_resource(template, database_allocated_storage_parameter,
                             database_class_parameter, database_username_parameter, database_password_parameter,
                             database_security_group_resource, database_subnet_group_resource):
    return template.add_resource(
        rds.DBInstance(
            'Database',
            DBName=Ref(database_username_parameter),
            AllocatedStorage=Ref(database_allocated_storage_parameter),
            DBInstanceClass=Ref(database_class_parameter),
            Engine='MySQL',
            EngineVersion='5.7',
            MasterUsername=Ref(database_username_parameter),
            MasterUserPassword=Ref(database_password_parameter),
            VPCSecurityGroups=[
                GetAtt(database_security_group_resource, 'GroupId')],
            DBSubnetGroupName=Ref(database_subnet_group_resource),
            PubliclyAccessible=False
        )
    )


def create_redis_subnet_group_resource(template, subnets_parameter):
    return template.add_resource(
        elasticache.SubnetGroup(
            'RedisSubnetGroup',
            Description='Subnets available for the Redis cluster',
            SubnetIds=Ref(subnets_parameter)
        )
    )


def create_redis_resource(template, redis_node_class_parameter, redis_nodes_count_parameter, redis_security_group_resource,
                          redis_subnet_group_resource):
    return template.add_resource(
        elasticache.CacheCluster(
            'Redis',
            Engine='redis',
            EngineVersion='4.0',
            CacheNodeType=Ref(redis_node_class_parameter),
            NumCacheNodes=Ref(redis_nodes_count_parameter),
            VpcSecurityGroupIds=[
                GetAtt(redis_security_group_resource, 'GroupId')],
            CacheSubnetGroupName=Ref(redis_subnet_group_resource)
        )
    )


def create_default_queue_resource(template, default_queue_name_variable):
    return template.add_resource(
        sqs.Queue(
            'DefaultQueue',
            QueueName=default_queue_name_variable
        )
    )


def create_notifications_queue_resource(template, notifications_queue_name_variable):
    return template.add_resource(
        sqs.Queue(
            'NotificationsQueue',
            QueueName=notifications_queue_name_variable
        )
    )


def create_search_queue_resource(template, search_queue_name_variable):
    return template.add_resource(
        sqs.Queue(
            'SearchQueue',
            QueueName=search_queue_name_variable
        )
    )


def create_uploads_bucket_resource(template, uploads_bucket_name_variable):
    return template.add_resource(
        s3.Bucket(
            'UploadsBucket',
            BucketName=uploads_bucket_name_variable,
            AccessControl='Private'
        )
    )


def create_ecs_cluster_role_resource(template):
    return template.add_resource(
        iam.Role(
            'ECSClusterRole',
            ManagedPolicyArns=[
                'arn:aws:iam::aws:policy/service-role/AmazonEC2ContainerServiceforEC2Role'],
            AssumeRolePolicyDocument={
                'Version': '2012-10-17',
                'Statement': [
                    {
                        'Action': 'sts:AssumeRole',
                        'Principal': {
                            'Service': 'ec2.amazonaws.com'
                        },
                        'Effect': 'Allow'
                    }
                ]
            }
        )
    )


def create_ec2_instance_profile_resource(template, ecs_cluster_role_resource):
    return template.add_resource(
        iam.InstanceProfile(
            'EC2InstanceProfile',
            Roles=[Ref(ecs_cluster_role_resource)]
        )
    )


def create_ecs_cluster_resource(template):
    return template.add_resource(
        ecs.Cluster(
            'ApiCluster'
        )
    )


def create_launch_template_resource(template, api_launch_template_name_variable, api_instance_class_parameter,
                                    ec2_instance_profile_resource, api_security_group_resource, ecs_cluster_resource):
    return template.add_resource(
        ec2.LaunchTemplate(
            'LaunchTemplate',
            LaunchTemplateName=api_launch_template_name_variable,
            LaunchTemplateData=ec2.LaunchTemplateData(
                ImageId='ami-0f82969826859fb14',
                InstanceType=Ref(api_instance_class_parameter),
                IamInstanceProfile=ec2.IamInstanceProfile(
                    Arn=GetAtt(ec2_instance_profile_resource, 'Arn')
                ),
                InstanceInitiatedShutdownBehavior='terminate',
                Monitoring=ec2.Monitoring(Enabled=True),
                SecurityGroups=[Ref(api_security_group_resource)],
                BlockDeviceMappings=[
                    ec2.LaunchTemplateBlockDeviceMapping(
                        DeviceName='/dev/xvdcz',
                        Ebs=ec2.EBSBlockDevice(
                            DeleteOnTermination=True,
                            VolumeSize=22,
                            VolumeType='gp2'
                        )
                    )
                ],
                UserData=Base64(
                    Join('', [
                        '#!/bin/bash\n',
                        'echo ECS_CLUSTER=',
                        Ref(ecs_cluster_resource),
                        ' >> /etc/ecs/ecs.config;echo ECS_BACKEND_HOST= >> /etc/ecs/ecs.config;'
                    ])
                )
            )
        )
    )


def create_docker_repository_resource(template, docker_repository_name_variable):
    return template.add_resource(
        ecr.Repository(
            'DockerRepository',
            RepositoryName=docker_repository_name_variable,
            LifecyclePolicy=ecr.LifecyclePolicy(
                LifecyclePolicyText='{"rules":[{"rulePriority":1,"description":"Remove untagged images older than 1 week","selection":{"tagStatus":"untagged","countType":"sinceImagePushed","countUnit":"days","countNumber":7},"action":{"type":"expire"}}]}'
            )
        )
    )


def create_api_log_group_resource(template, api_log_group_name_variable):
    return template.add_resource(
        logs.LogGroup(
            'ApiLogGroup',
            LogGroupName=api_log_group_name_variable,
            RetentionInDays=7
        )
    )


def create_queue_worker_log_group_resource(template, queue_worker_log_group_name_variable):
    return template.add_resource(
        logs.LogGroup(
            'QueueWorkerLogGroup',
            LogGroupName=queue_worker_log_group_name_variable,
            RetentionInDays=7
        )
    )


def create_scheduler_log_group_resource(template, scheduler_log_group_name_variable):
    return template.add_resource(
        logs.LogGroup(
            'SchedulerLogGroup',
            LogGroupName=scheduler_log_group_name_variable,
            RetentionInDays=7
        )
    )


def create_elasticsearch_log_group_resource(template, elasticsearch_log_group_name_variable):
    return template.add_resource(
        logs.LogGroup(
            'SearchLogGroup',
            LogGroupName=elasticsearch_log_group_name_variable,
            RetentionInDays=7
        )
    )


def create_elasticsearch_lambda_log_group_resource(template, elasticsearch_log_access_policy_lambda_name_variable):
    return template.add_resource(
        logs.LogGroup(
            'SearchLambdaLogGroup',
            LogGroupName=Sub('/aws/lambda/${LambdaFunctionName}',
                             LambdaFunctionName=elasticsearch_log_access_policy_lambda_name_variable),
            RetentionInDays=7
        )
    )


def create_api_task_definition_resource(template, api_task_definition_family_variable, docker_repository_resource,
                                        api_log_group_resource):
    return template.add_resource(
        ecs.TaskDefinition(
            'ApiTaskDefinition',
            Family=api_task_definition_family_variable,
            NetworkMode='bridge',
            RequiresCompatibilities=['EC2'],
            ContainerDefinitions=[ecs.ContainerDefinition(
                Name='api',
                Image=Join('.', [
                    Ref('AWS::AccountId'),
                    'dkr.ecr',
                    Ref('AWS::Region'),
                    Join('/', [
                        'amazonaws.com',
                        Ref(docker_repository_resource)
                    ])
                ]),
                MemoryReservation='256',
                PortMappings=[ecs.PortMapping(
                    HostPort='0',
                    ContainerPort='80',
                    Protocol='tcp'
                )],
                Essential=True,
                LogConfiguration=ecs.LogConfiguration(
                    LogDriver='awslogs',
                    Options={
                        'awslogs-group': Ref(api_log_group_resource),
                        'awslogs-region': Ref('AWS::Region'),
                        'awslogs-stream-prefix': 'ecs'
                    }
                )
            )]
        )
    )


def create_queue_worker_task_definition_resource(template, queue_worker_task_definition_family_variable,
                                                 docker_repository_resource, queue_worker_log_group_resource,
                                                 default_queue_name_variable, notifications_queue_name_variable,
                                                 search_queue_name_variable):
    return template.add_resource(
        ecs.TaskDefinition(
            'QueueWorkerTaskDefinition',
            Family=queue_worker_task_definition_family_variable,
            NetworkMode='bridge',
            RequiresCompatibilities=['EC2'],
            ContainerDefinitions=[ecs.ContainerDefinition(
                Name='api',
                Image=Join('.', [
                    Ref('AWS::AccountId'),
                    'dkr.ecr',
                    Ref('AWS::Region'),
                    Join('/', [
                        'amazonaws.com',
                        Ref(docker_repository_resource)
                    ])
                ]),
                MemoryReservation='256',
                Essential=True,
                LogConfiguration=ecs.LogConfiguration(
                    LogDriver='awslogs',
                    Options={
                        'awslogs-group': Ref(queue_worker_log_group_resource),
                        'awslogs-region': Ref('AWS::Region'),
                        'awslogs-stream-prefix': 'ecs'
                    }
                ),
                Command=[
                    'php',
                    'artisan',
                    'queue:work',
                    '--tries=1',
                    '--queue=default,notifications,search'
                ],
                WorkingDirectory='/var/www/html',
                HealthCheck=ecs.HealthCheck(
                    Command=[
                        'CMD-SHELL',
                        'php -v || exit 1'
                    ],
                    Interval=30,
                    Retries=3,
                    Timeout=5
                )
            )]
        )
    )


def create_scheduler_task_definition_resource(template, scheduler_task_definition_family_variable,
                                              docker_repository_name_variable, scheduler_log_group_resource):
    return template.add_resource(
        ecs.TaskDefinition(
            'SchedulerTaskDefinition',
            Family=scheduler_task_definition_family_variable,
            NetworkMode='bridge',
            RequiresCompatibilities=['EC2'],
            ContainerDefinitions=[ecs.ContainerDefinition(
                Name='api',
                Image=Join('.', [
                    Ref('AWS::AccountId'),
                    'dkr.ecr',
                    Ref('AWS::Region'),
                    Join('/', [
                        'amazonaws.com',
                        docker_repository_name_variable
                    ])
                ]),
                MemoryReservation='256',
                Essential=True,
                LogConfiguration=ecs.LogConfiguration(
                    LogDriver='awslogs',
                    Options={
                        'awslogs-group': Ref(scheduler_log_group_resource),
                        'awslogs-region': Ref('AWS::Region'),
                        'awslogs-stream-prefix': 'ecs'
                    }
                ),
                Command=[
                    'php',
                    'artisan',
                    'ck:run-scheduler'
                ],
                WorkingDirectory='/var/www/html',
                HealthCheck=ecs.HealthCheck(
                    Command=[
                        'CMD-SHELL',
                        'php -v || exit 1'
                    ],
                    Interval=30,
                    Retries=3,
                    Timeout=5
                )
            )]
        )
    )


def create_load_balancer_resource(template, load_balancer_security_group_resource, subnets_parameter):
    return template.add_resource(
        elb.LoadBalancer(
            'LoadBalancer',
            Scheme='internet-facing',
            SecurityGroups=[
                GetAtt(load_balancer_security_group_resource, 'GroupId')],
            Subnets=Ref(subnets_parameter),
        )
    )


def create_api_target_group_resource(template, vpc_parameter, load_balancer_resource):
    return template.add_resource(
        elb.TargetGroup(
            'ApiTargetGroup',
            HealthCheckIntervalSeconds=30,
            HealthCheckPath='/',
            HealthCheckPort='traffic-port',
            HealthCheckProtocol='HTTP',
            HealthCheckTimeoutSeconds=5,
            HealthyThresholdCount=5,
            UnhealthyThresholdCount=2,
            Port=80,
            Protocol='HTTP',
            TargetType='instance',
            VpcId=Ref(vpc_parameter),
            DependsOn=[load_balancer_resource]
        )
    )


def create_load_balancer_listener_resource(template, load_balancer_resource, api_target_group_resource,
                                           certificate_arn_parameter):
    return template.add_resource(
        elb.Listener(
            'LoadBalancerListener',
            LoadBalancerArn=Ref(load_balancer_resource),
            Port=443,
            Protocol='HTTPS',
            DefaultActions=[elb.Action(
                Type='forward',
                TargetGroupArn=Ref(api_target_group_resource)
            )],
            Certificates=[
                elb.Certificate(
                    CertificateArn=Ref(certificate_arn_parameter)
                )
            ]
        )
    )


def create_ecs_service_role_resource(template):
    return template.add_resource(
        iam.Role(
            'ECSServiceRole',
            AssumeRolePolicyDocument={
                'Version': '2012-10-17',
                'Statement': [
                    {
                        'Action': 'sts:AssumeRole',
                        'Effect': 'Allow',
                        'Principal': {
                            'Service': 'ecs.amazonaws.com'
                        }
                    }
                ]
            },
            Policies=[
                iam.Policy(
                    PolicyName='ECSServiceRolePolicy',
                    PolicyDocument={
                        'Statement': [
                            {
                                'Effect': 'Allow',
                                'Action': [
                                    'ec2:AttachNetworkInterface',
                                    'ec2:CreateNetworkInterface',
                                    'ec2:CreateNetworkInterfacePermission',
                                    'ec2:DeleteNetworkInterface',
                                    'ec2:DeleteNetworkInterfacePermission',
                                    'ec2:Describe*',
                                    'ec2:DetachNetworkInterface',
                                    'elasticloadbalancing:DeregisterInstancesFromLoadBalancer',
                                    'elasticloadbalancing:DeregisterTargets',
                                    'elasticloadbalancing:Describe*',
                                    'elasticloadbalancing:RegisterInstancesWithLoadBalancer',
                                    'elasticloadbalancing:RegisterTargets',
                                    'route53:ChangeResourceRecordSets',
                                    'route53:CreateHealthCheck',
                                    'route53:DeleteHealthCheck',
                                    'route53:Get*',
                                    'route53:List*',
                                    'route53:UpdateHealthCheck',
                                    'servicediscovery:DeregisterInstance',
                                    'servicediscovery:Get*',
                                    'servicediscovery:List*',
                                    'servicediscovery:RegisterInstance',
                                    'servicediscovery:UpdateInstanceCustomHealthStatus'
                                ],
                                'Resource': '*'
                            },
                            {
                                'Effect': 'Allow',
                                'Action': [
                                    'ec2:CreateTags'
                                ],
                                'Resource': 'arn:aws:ec2:*:*:network-interface/*'
                            }
                        ]
                    }
                )
            ]
        )
    )


def create_api_service_resource(template, ecs_cluster_resource, api_task_definition_resource, api_task_count_parameter,
                                api_target_group_resource, ecs_service_role_resource, load_balancer_listener_resource):
    return template.add_resource(
        ecs.Service(
            'ApiService',
            ServiceName='api',
            Cluster=Ref(ecs_cluster_resource),
            TaskDefinition=Ref(api_task_definition_resource),
            DeploymentConfiguration=ecs.DeploymentConfiguration(
                MinimumHealthyPercent=100,
                MaximumPercent=200
            ),
            DesiredCount=Ref(api_task_count_parameter),
            LaunchType='EC2',
            LoadBalancers=[ecs.LoadBalancer(
                ContainerName='api',
                ContainerPort=80,
                TargetGroupArn=Ref(api_target_group_resource)
            )],
            Role=Ref(ecs_service_role_resource),
            DependsOn=[load_balancer_listener_resource]
        )
    )


def create_queue_worker_service_resource(template, ecs_cluster_resource, queue_worker_task_definition_resource,
                                         queue_worker_task_count_parameter):
    return template.add_resource(
        ecs.Service(
            'QueueWorkerService',
            ServiceName='queue-worker',
            Cluster=Ref(ecs_cluster_resource),
            TaskDefinition=Ref(queue_worker_task_definition_resource),
            DeploymentConfiguration=ecs.DeploymentConfiguration(
                MinimumHealthyPercent=0,
                MaximumPercent=100
            ),
            DesiredCount=Ref(queue_worker_task_count_parameter),
            LaunchType='EC2'
        )
    )


def create_scheduler_service_resource(template, ecs_cluster_resource, scheduler_task_definition_resource,
                                      scheduler_task_count_parameter):
    return template.add_resource(
        ecs.Service(
            'SchedulerService',
            ServiceName='scheduler',
            Cluster=Ref(ecs_cluster_resource),
            TaskDefinition=Ref(scheduler_task_definition_resource),
            DeploymentConfiguration=ecs.DeploymentConfiguration(
                MinimumHealthyPercent=0,
                MaximumPercent=100
            ),
            DesiredCount=Ref(scheduler_task_count_parameter),
            LaunchType='EC2'
        )
    )


def create_autoscaling_group_resource(template, api_instance_count_parameter, launch_template_resource):
    return template.add_resource(
        autoscaling.AutoScalingGroup(
            'AutoScalingGroup',
            DesiredCapacity=Ref(api_instance_count_parameter),
            MinSize=Ref(api_instance_count_parameter),
            MaxSize=Ref(api_instance_count_parameter),
            LaunchTemplate=autoscaling.LaunchTemplateSpecification(
                LaunchTemplateId=Ref(launch_template_resource),
                Version=GetAtt(launch_template_resource, 'LatestVersionNumber')
            ),
            AvailabilityZones=['eu-west-2a', 'eu-west-2b', 'eu-west-2c']
        )
    )


def create_api_user_resource(template, api_user_name_variable, uploads_bucket_resource, default_queue_resource,
                             notifications_queue_resource, search_queue_resource):
    return template.add_resource(
        iam.User(
            'ApiUser',
            UserName=api_user_name_variable,
            Policies=[
                iam.Policy(
                    PolicyName='ApiUserPolicy',
                    PolicyDocument={
                        'Version': '2012-10-17',
                        'Statement': [
                            {
                                'Action': 's3:*',
                                'Effect': 'Allow',
                                'Resource': [
                                    GetAtt(uploads_bucket_resource, 'Arn'),
                                    Join(
                                        '/', [GetAtt(uploads_bucket_resource, 'Arn'), '*'])
                                ]
                            },
                            {
                                'Action': 'sqs:*',
                                'Effect': 'Allow',
                                'Resource': GetAtt(default_queue_resource, 'Arn')
                            },
                            {
                                'Action': 'sqs:*',
                                'Effect': 'Allow',
                                'Resource': GetAtt(notifications_queue_resource, 'Arn')
                            },
                            {
                                'Action': 'sqs:*',
                                'Effect': 'Allow',
                                'Resource': GetAtt(search_queue_resource, 'Arn')
                            }
                        ]
                    }
                )
            ]
        )
    )


def create_ci_user_resource(template, ci_user_name_variable):
    return template.add_resource(
        iam.User(
            'CiUser',
            UserName=ci_user_name_variable,
            Policies=[
                iam.Policy(
                    PolicyName='CiUserPolicy',
                    PolicyDocument={
                        'Version': '2012-10-17',
                        'Statement': [
                            {
                                'Action': 'ecr:*',
                                'Effect': 'Allow',
                                'Resource': '*'
                            },
                            {
                                'Action': 'ecs:UpdateService',
                                'Effect': 'Allow',
                                'Resource': '*'
                            },
                            {
                                'Action': 'secretsmanager:GetSecretValue',
                                'Effect': 'Allow',
                                'Resource': '*'
                            }
                        ]
                    }
                )
            ]
        )
    )


def create_elasticsearch_lambda_execution_role_resource(template, lambda_function_name_variable, elasticsearch_log_group_name_variable):
    return template.add_resource(
        iam.Role(
            'LambdaExecutionRole',
            AssumeRolePolicyDocument={
                'Version': '2012-10-17',
                'Statement': [
                    {
                        'Action': 'sts:AssumeRole',
                        'Effect': 'Allow',
                        'Principal': {
                            'Service': 'lambda.amazonaws.com'
                        }
                    }
                ]
            },
            Policies=[
                iam.Policy(
                    PolicyName='LambdaExecutionRoleCreateLogStreamPolicy',
                    PolicyDocument={
                        'Statement': [
                            {
                                'Effect': 'Allow',
                                'Action': [
                                    'logs:CreateLogStream',
                                    'logs:PutLogEvents',
                                ],
                                'Resource': Sub('arn:aws:logs:${AWS::Region}:${AWS::AccountId}:log-group:/aws/lambda/${LambdaFunctionName}:log-stream:*', LambdaFunctionName=lambda_function_name_variable)
                            },
                        ]
                    }
                ),
                iam.Policy(
                    PolicyName='LambdaExecutionRoleCreateLogGroupPolicy',
                    PolicyDocument={
                        'Statement': [
                            {
                                'Effect': 'Allow',
                                'Action': [
                                    'logs:CreateLogGroup',
                                ],
                                'Resource': [
                                    Sub('arn:aws:logs:${AWS::Region}:${AWS::AccountId}:log-group:/aws/lambda/${LambdaFunctionName}',
                                        LambdaFunctionName=lambda_function_name_variable),
                                    Sub('arn:aws:logs:${AWS::Region}:${AWS::AccountId}:log-group:${LogGroupName}',
                                        LogGroupName=elasticsearch_log_group_name_variable),
                                ]
                            },
                            {
                                'Effect': 'Allow',
                                'Action': [
                                    'logs:PutResourcePolicy',
                                    'logs:DeleteResourcePolicy'
                                ],
                                'Resource': Sub('arn:aws:logs:${AWS::Region}:${AWS::AccountId}:*')
                            }
                        ]
                    }
                )
            ]
        )
    )


def create_elasticsearch_security_group_resource(template, api_security_group_resource):
    return template.add_resource(
        ec2.SecurityGroup(
            'ElasticsearchSecurityGroup',
            GroupDescription='For connecting to the Elasticsearch service',
            SecurityGroupIngress=[
                ec2.SecurityGroupRule(
                    Description='HTTPS access from the API containers',
                    IpProtocol='tcp',
                    FromPort='443',
                    ToPort='443',
                    SourceSecurityGroupName=Ref(api_security_group_resource)
                )
            ]
        )
    )


def create_elasticsearch_lambda_log_group_policy_function_resource(template, lambda_function_name_variable, elasticsearch_lambda_log_group_resource, elasticsearch_lambda_execution_role_resource):
    return template.add_resource(
        awslambda.Function(
            "ElasticsearchLambdaLogGroupPolicyFunction",
            FunctionName=lambda_function_name_variable,
            DependsOn=elasticsearch_lambda_log_group_resource,
            Code=awslambda.Code(ZipFile=Join("\n", [
                "import urllib3",
                "import json",
                "import boto3",
                "http = urllib3.PoolManager()",
                "SUCCESS = 'SUCCESS'",
                "FAILED = 'FAILED'",
                "def send(event, context, responseStatus, responseData, physicalResourceId=None, noEcho=False):",
                "    responseUrl = event['ResponseURL']",
                "    print(responseUrl)",
                "    responseBody = {}",
                "    responseBody['Status'] = responseStatus",
                "    responseBody['Reason'] = 'See the details in CloudWatch Log Stream: ' + context.log_stream_name",
                "    responseBody['PhysicalResourceId'] = physicalResourceId or context.log_stream_name",
                "    responseBody['StackId'] = event['StackId']",
                "    responseBody['RequestId'] = event['RequestId']",
                "    responseBody['LogicalResourceId'] = event['LogicalResourceId']",
                "    responseBody['NoEcho'] = noEcho",
                "    responseBody['Data'] = responseData",
                "    json_responseBody = json.dumps(responseBody)",
                "    print(\"Response body:\\n\" + json_responseBody)",
                "    headers = {",
                "        'content-type' : '',",
                "        'content-length' : str(len(json_responseBody))",
                "    }",
                "    try:",
                "        response = http.request('PUT',responseUrl,body=json_responseBody.encode('utf-8'),headers=headers)",
                "        print('Status code: ' + response.reason)",
                "    except Exception as e:",
                "        print('send(..) failed executing requests.put(..): ' + str(e))",
                "def handler(event, context):",
                "    logsgroup_policy_name=event['ResourceProperties']['CWLOGS_NAME']",
                "    cw_log_group_arn=event['ResourceProperties']['CWLOGS_ARN']",
                "    cwlogs = boto3.client('logs')",
                "    loggroup_policy={",
                "        'Version': '2012-10-17',",
                "        'Statement': [{",
                "        'Sid': '',",
                "        'Effect': 'Allow',",
                "        'Principal': { 'Service': 'es.amazonaws.com'},",
                "        'Action':[",
                "        'logs:PutLogEvents',",
                "        'logs:PutLogEventsBatch',",
                "        'logs:CreateLogStream'",
                "            ],",
                "        'Resource': f'{cw_log_group_arn}'",
                "        }]",
                "        }",
                "    loggroup_policy = json.dumps(loggroup_policy)",
                "    if(event['RequestType'] == 'Delete'):",
                "        print('Request Type:',event['RequestType'])",
                "        cwlogs.delete_resource_policy(",
                "        policyName=logsgroup_policy_name",
                "        )",
                "        responseData={}",
                "        send(event, context, SUCCESS, responseData)",
                "    elif(event['RequestType'] == 'Create'):",
                "        try:",
                "            cwlogs.put_resource_policy(",
                "            policyName = logsgroup_policy_name,",
                "            policyDocument = loggroup_policy",
                "            )",
                "            responseData={}",
                "            print('Sending response to custom resource')",
                "            send(event, context, SUCCESS, responseData)",
                "        except Exception as  e:",
                "            print('Failed to process:', e)",
                "            send(event, context, FAILED, responseData)",
                "    elif(event['RequestType'] == 'Update'):",
                "        try:",
                "            responseData={}",
                "            print('Update is not supported on this resource')",
                "            send(event, context, SUCCESS, responseData)",
                "        except Exception as  e:",
                "            print('Failed to process:', e)",
                "            send(event, context, FAILED, responseData)",
            ])),
            Handler="index.handler",
            Role=GetAtt(elasticsearch_lambda_execution_role_resource, 'Arn'),
            Runtime="python3.9"
        )
    )


def create_elasticsearch_log_group_policy_custom_resource(template, elasticsearch_lambda_log_group_policy_function_resource, elasticsearch_log_group_name_variable, elasticsearch_log_group_resource):
    return template.add_resource(
        CustomLogGroupPolicy(
            'ElasticsearchCustomLogGroupPolicy',
            ServiceToken=GetAtt(
                elasticsearch_lambda_log_group_policy_function_resource, 'Arn'),
            CWLOGS_NAME=elasticsearch_log_group_name_variable,
            CWLOGS_ARN=GetAtt(elasticsearch_log_group_resource, 'Arn')
        )
    )

def create_elasticsearch_service_linked_role_resource(template):
    return template.add_resource(
        iam.ServiceLinkedRole(
            'ElasticsearchServiceLinkedRole',
            AWSServiceName='es.amazonaws.com'
        )
    )

def create_elasticsearch_resource(template, elasticsearch_domain_name_variable,
                                  elasticsearch_instance_count_parameter, elasticsearch_instance_class_parameter,
                                  elasticsearch_security_group_resource, subnets_parameter):
    return template.add_resource(
        elasticsearch.Domain(
            'Elasticsearch',
            DependsOn='ElasticsearchCustomLogGroupPolicy',
            AccessPolicies={
                'Version': '2012-10-17',
                'Statement': [
                    {
                        'Effect': 'Allow',
                        'Principal': {
                            'AWS': '*'
                        },
                        'Action': 'es:*',
                        'Resource': Sub('arn:aws:es:${AWS::Region}:${AWS::AccountId}:domain/${DomainName}/*',
                                        DomainName=elasticsearch_domain_name_variable)
                    }
                ]
            },
            DomainName=elasticsearch_domain_name_variable,
            EBSOptions=elasticsearch.EBSOptions(
                EBSEnabled=True,
                VolumeSize=10,
                VolumeType='gp2'
            ),
            ElasticsearchClusterConfig=elasticsearch.ElasticsearchClusterConfig(
                InstanceCount=Ref(elasticsearch_instance_count_parameter),
                InstanceType=Ref(elasticsearch_instance_class_parameter)
            ),
            ElasticsearchVersion='7.9',
            VPCOptions=elasticsearch.VPCOptions(
                SecurityGroupIds=[
                    GetAtt(elasticsearch_security_group_resource, 'GroupId')],
                SubnetIds=[Select(0, Ref(subnets_parameter))]
            )
        )
    )
