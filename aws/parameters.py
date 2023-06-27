from troposphere import Parameter


def create_uuid_parameter(template, uuid):
    return template.add_parameter(
        Parameter(
            'Uuid',
            Type='String',
            Default=uuid,
            Description='The unique ID for this stack.',
            MinLength='36',
            MaxLength='36'
        )
    )


def create_environment_parameter(template):
    return template.add_parameter(
        Parameter(
            'Environment',
            Type='String',
            Description='The environment this stack is for (e.g. production or staging).',
            MinLength='1'
        )
    )


def create_certificate_arn_parameter(template):
    return template.add_parameter(
        Parameter(
            'CertificateArn',
            Type='String',
            Description='The ARN for the API load balancer SSL certificate.'
        )
    )


def create_vpc_parameter(template):
    return template.add_parameter(
        Parameter(
            'Vpc',
            Type='AWS::EC2::VPC::Id',
            Description='The Virtual Private Cloud (VPC) to launch the stack in.'
        )
    )


def create_subnets_parameter(template):
    return template.add_parameter(
        Parameter(
            'Subnets',
            Type='List<AWS::EC2::Subnet::Id>',
            Description='The list of subnet IDs, for at least two Availability Zones in the region in your Virtual Private Cloud (VPC).'
        )
    )


def create_database_username_parameter(template):
    return template.add_parameter(
        Parameter(
            'DatabaseUsername',
            Type='String',
            Description='The database name and admin username',
            MinLength='8',
            MaxLength='36',
            AllowedPattern='[a-zA-Z0-9_]*',
            ConstraintDescription='Must be at least 8 characters and only contain alphanumeric characters.'
        )
    )


def create_database_password_parameter(template):
    return template.add_parameter(
        Parameter(
            'DatabasePassword',
            Description='The database admin password.',
            NoEcho=True,
            Type='String',
            MinLength='8',
            MaxLength='41',
            AllowedPattern='[a-zA-Z0-9]*',
            ConstraintDescription='Must be at least 8 characters and only contain alphanumeric characters.'
        )
    )


def create_database_class_parameter(template):
    return template.add_parameter(
        Parameter(
            'DatabaseClass',
            Description='The database instance class.',
            Type='String',
            Default='db.t3.micro',
            AllowedValues=[
                'db.t3.micro',
                'db.t3.small',
                'db.t3.medium',
                'db.t3.large',
                'db.t3.xlarge',
                'db.t3.2xlarge'
            ],
            ConstraintDescription='Must select a valid database instance type.'
        )
    )


def create_database_allocated_storage_parameter(template):
    return template.add_parameter(
        Parameter(
            'DatabaseAllocatedStorage',
            Description='The size of the database (GiB).',
            Default='10',
            Type='Number',
            MinValue='5',
            MaxValue='1024',
            ConstraintDescription='Must be between 5 and 1024 GiB.'
        )
    )


def create_redis_node_class_parameter(template):
    return template.add_parameter(
        Parameter(
            'RedisNodeClass',
            Description='The Redis node class.',
            Type='String',
            Default='cache.t2.micro',
            AllowedValues=[
                'cache.t2.micro',
                'cache.t2.small',
                'cache.t2.medium'
            ],
            ConstraintDescription='Must select a valid Redis node type.'
        )
    )


def create_redis_nodes_count_parameter(template):
    return template.add_parameter(
        Parameter(
            'RedisNodesCount',
            Description='The number of Redis nodes to have in the cluster.',
            Default='1',
            Type='Number',
            MinValue='1',
            ConstraintDescription='Must be 1 or more.'
        )
    )


def create_api_instance_class_parameter(template):
    return template.add_parameter(
        Parameter(
            'ApiInstanceClass',
            Description='The API EC2 instance class.',
            Type='String',
            Default='t3.micro',
            AllowedValues=[
                't3.nano',
                't3.micro',
                't3.small',
                't3.medium',
                't3.large',
                't3.xlarge',
                't3.2xlarge'
            ],
            ConstraintDescription='Must select a valid API instance type.'
        )
    )


def create_api_instance_count_parameter(template):
    return template.add_parameter(
        Parameter(
            'ApiInstanceCount',
            Description='The number of API EC2 instances to load balance between.',
            Type='Number',
            Default='2',
            MinValue='1',
            ConstraintDescription='Must be 1 or more.'
        )
    )


def create_api_task_count_parameter(template):
    return template.add_parameter(
        Parameter(
            'ApiTaskCount',
            Description='The number of API containers to run.',
            Type='Number',
            Default='0',
            MinValue='0',
            ConstraintDescription='Must be 0 or more.'
        )
    )


def create_scheduler_task_count_parameter(template):
    return template.add_parameter(
        Parameter(
            'SchedulerTaskCount',
            Description='The number of scheduler containers to run.',
            Type='Number',
            Default='0',
            MinValue='0',
            MaxValue='1',
            ConstraintDescription='Must be either 0 or 1.'
        )
    )


def create_queue_worker_task_count_parameter(template):
    return template.add_parameter(
        Parameter(
            'QueueWorkerTaskCount',
            Description='The number of queue worker containers to run.',
            Type='Number',
            Default='0',
            MinValue='0',
            ConstraintDescription='Must be 0 or more.'
        )
    )


def create_elasticsearch_instance_class_parameter(template):
    return template.add_parameter(
        Parameter(
            'ElasticsearchInstanceClass',
            Description='The Elasticseach instance class.',
            Type='String',
            Default='t3.small.elasticsearch',
            AllowedValues=[
                't3.small.elasticsearch',
                't3.medium.elasticsearch'
            ],
            ConstraintDescription='Must select a valid Elasticsearch instance type.'
        )
    )


def create_elasticsearch_instance_count_parameter(template):
    return template.add_parameter(
        Parameter(
            'ElasticsearchInstanceCount',
            Description='The number of Elasticsearch nodes to run.',
            Type='Number',
            Default='1',
            MinValue='1',
            ConstraintDescription='Must be 1 or more.'
        )
    )

def create_cname_parameter(template):
    return template.add_parameter(
    Parameter(
        'Cname',
        Type='String',
        Description='The site CNAME.',
        MinLength='4',
        AllowedPattern='^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$',
        ConstraintDescription='Must be a valid domain'
    )
)
