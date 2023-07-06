from troposphere import Output, Ref, GetAtt, Sub


def create_database_name_output(template, database_username_variable):
    return template.add_output(
        Output(
            'DatabaseName',
            Description='The database name',
            Value=Ref(database_username_variable)
        )
    )


def create_database_username_output(template, database_username_variable):
    return template.add_output(
        Output(
            'DatabaseUsername',
            Description='The username for the database',
            Value=Ref(database_username_variable)
        )
    )


def create_database_host_output(template, database_resource):
    return template.add_output(
        Output(
            'DatabaseHost',
            Description='The host of the RDS instance',
            Value=GetAtt(database_resource, 'Endpoint.Address')
        )
    )


def create_database_port_output(template, database_resource):
    return template.add_output(
        Output(
            'DatabasePort',
            Description='The port of the RDS instance',
            Value=GetAtt(database_resource, 'Endpoint.Port')
        )
    )


def create_redis_host_output(template, redis_resource):
    return template.add_output(
        Output(
            'RedisHost',
            Description='The host of the Redis instance',
            Value=GetAtt(redis_resource, 'RedisEndpoint.Address')
        )
    )


def create_redis_port_output(template, redis_resource):
    return template.add_output(
        Output(
            'RedisPort',
            Description='The port of the Redis instance',
            Value=GetAtt(redis_resource, 'RedisEndpoint.Port')
        )
    )


def create_default_queue_output(template, default_queue_name_variable):
    return template.add_output(
        Output(
            'DefaultQueue',
            Description='The name of the default queue',
            Value=default_queue_name_variable
        )
    )


def create_notifications_queue_output(template, notifications_queue_name_variable):
    return template.add_output(
        Output(
            'NotificationsQueue',
            Description='The name of the notifications queue',
            Value=notifications_queue_name_variable
        )
    )


def create_default_queue_url_output(template, default_queue_resource):
    return template.add_output(
        Output(
            'DefaultQueueUrl',
            Description='The URL for the defalt queue',
            Value=Ref(default_queue_resource)
        )
    )


def create_load_balancer_domain_output(template, load_balancer_resource):
    return template.add_output(
        Output(
            'LoadBalancerDomain',
            Description='The domain name of the load balancer',
            Value=GetAtt(load_balancer_resource, 'DNSName')
        )
    )


def create_elasticsearch_service_linked_role_id_output(template, elasticsearch_service_linked_role):
    return template.add_output(
        Output(
            'ElasticsearchServiceLinkedRoleId',
            Description='The ID of the Service Linked Role used by the Elasticsearch service',
            Value=Ref(elasticsearch_service_linked_role)
        )
    )

def create_elasticsearch_host_output(template, elasticsearch_resource):
    return template.add_output(
        Output(
            'ElasticsearchHost',
            Description='The host of the Elasticsearch instance',
            Value=GetAtt(elasticsearch_resource, 'DomainEndpoint')
        )
    )


def create_docker_repository_uri_output(template, docker_repository_resource):
    return template.add_output(
        Output(
            'DockerRepositoryUri',
            Description='The URI of the Docker repository',
            Value=Sub('${AWS::AccountId}.dkr.ecr.${AWS::Region}.amazonaws.com/${RepositoryName}',
                      RepositoryName=Ref(docker_repository_resource))
        )
    )


def create_docker_cluster_name_output(template, ecs_cluster_resource):
    return template.add_output(
        Output(
            'DockerClusterName',
            Description='The name of the Docker cluster',
            Value=Ref(ecs_cluster_resource)
        )
    )


def create_uploads_bucket_name_output(template, uploads_bucket_name_variable):
    return template.add_output(
        Output(
            'UploadsBucketName',
            Description='The name of the S3 bucket for uploads',
            Value=uploads_bucket_name_variable
        )
    )
