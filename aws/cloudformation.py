import uuid
import json
from template import create_template

from parameters import create_uuid_parameter, create_environment_parameter, create_certificate_arn_parameter, \
    create_vpc_parameter, create_cname_parameter, create_database_username_parameter,\
    create_subnets_parameter, create_database_password_parameter, create_database_class_parameter, \
    create_database_allocated_storage_parameter, create_redis_node_class_parameter, create_redis_nodes_count_parameter, \
    create_api_instance_class_parameter, create_api_instance_count_parameter, create_api_task_count_parameter, \
    create_scheduler_task_count_parameter, create_queue_worker_task_count_parameter, \
    create_elasticsearch_instance_class_parameter, create_elasticsearch_instance_count_parameter

from variables import create_default_queue_name_variable, create_notifications_queue_name_variable, \
    create_search_queue_name_variable, create_uploads_bucket_name_variable, create_api_launch_template_name_variable, \
    create_docker_repository_name_variable, create_api_log_group_name_variable, create_queue_worker_log_group_name_variable, \
    create_scheduler_log_group_name_variable, create_elasticsearch_log_group_name_variable, create_api_task_definition_family_variable, \
    create_queue_worker_task_definition_family_variable, create_scheduler_task_definition_family_variable, \
    create_api_user_name_variable, create_ci_user_name_variable, create_api_name_variable, create_elasticsearch_domain_name_variable, create_elasticsearch_log_access_policy_lambda_name_variable

from resources import create_load_balancer_security_group_resource, create_api_security_group_resource, \
    create_database_security_group_resource, create_redis_security_group_resource, create_database_subnet_group_resource, \
    create_database_resource, create_redis_subnet_group_resource, create_redis_resource, create_default_queue_resource, \
    create_notifications_queue_resource, create_search_queue_resource, create_uploads_bucket_resource, \
    create_ecs_cluster_role_resource, create_ec2_instance_profile_resource, create_ecs_cluster_resource, \
    create_launch_template_resource, create_docker_repository_resource, create_api_log_group_resource, \
    create_queue_worker_log_group_resource, create_scheduler_log_group_resource, create_elasticsearch_log_group_resource, create_elasticsearch_lambda_log_group_resource, create_api_task_definition_resource, \
    create_queue_worker_task_definition_resource, create_scheduler_task_definition_resource, create_load_balancer_resource, \
    create_api_target_group_resource, create_load_balancer_listener_resource, create_ecs_service_role_resource, \
    create_api_service_resource, create_queue_worker_service_resource, create_scheduler_service_resource, \
    create_autoscaling_group_resource, create_api_user_resource, create_ci_user_resource, \
    create_elasticsearch_security_group_resource, create_elasticsearch_lambda_execution_role_resource, \
    create_elasticsearch_lambda_log_group_policy_function_resource, create_elasticsearch_log_group_policy_custom_resource, \
    create_elasticsearch_service_linked_role_resource, create_elasticsearch_resource

from outputs import create_database_name_output, create_database_username_output, create_database_host_output, \
    create_database_port_output, create_redis_host_output, create_redis_port_output, create_default_queue_url_output, \
    create_default_queue_output, create_notifications_queue_output, create_load_balancer_domain_output, create_elasticsearch_host_output, \
    create_elasticsearch_service_linked_role_id_output, create_docker_repository_uri_output, create_docker_cluster_name_output, \
    create_uploads_bucket_name_output

# UUID.
uuid = str(uuid.uuid4())

# Template.
api_name = create_api_name_variable()
template = create_template(api_name)

# Parameters.
uuid_parameter = create_uuid_parameter(template, uuid)
environment_parameter = create_environment_parameter(template)
cname_parameter = create_cname_parameter(template)
certificate_arn_parameter = create_certificate_arn_parameter(template)
vpc_parameter = create_vpc_parameter(template)
subnets_parameter = create_subnets_parameter(template)
database_username_parameter = create_database_username_parameter(template)
database_password_parameter = create_database_password_parameter(template)
database_class_parameter = create_database_class_parameter(template)
database_allocated_storage_parameter = create_database_allocated_storage_parameter(
    template)
redis_node_class_parameter = create_redis_node_class_parameter(template)
redis_nodes_count_parameter = create_redis_nodes_count_parameter(template)
api_instance_class_parameter = create_api_instance_class_parameter(template)
api_instance_count_parameter = create_api_instance_count_parameter(template)
api_task_count_parameter = create_api_task_count_parameter(template)
scheduler_task_count_parameter = create_scheduler_task_count_parameter(
    template)
queue_worker_task_count_parameter = create_queue_worker_task_count_parameter(
    template)
elasticsearch_instance_class_parameter = create_elasticsearch_instance_class_parameter(
    template)
elasticsearch_instance_count_parameter = create_elasticsearch_instance_count_parameter(
    template)

# Variables.
default_queue_name_variable = create_default_queue_name_variable(
    environment_parameter, uuid_parameter)
notifications_queue_name_variable = create_notifications_queue_name_variable(
    environment_parameter, uuid_parameter)
search_queue_name_variable = create_search_queue_name_variable(
    environment_parameter, uuid_parameter)
uploads_bucket_name_variable = create_uploads_bucket_name_variable(
    environment_parameter, uuid_parameter)
api_launch_template_name_variable = create_api_launch_template_name_variable(
    environment_parameter)
docker_repository_name_variable = create_docker_repository_name_variable(
    environment_parameter, uuid_parameter)
api_log_group_name_variable = create_api_log_group_name_variable(
    environment_parameter)
queue_worker_log_group_name_variable = create_queue_worker_log_group_name_variable(
    environment_parameter)
scheduler_log_group_name_variable = create_scheduler_log_group_name_variable(
    environment_parameter)
elasticsearch_log_group_name_variable = create_elasticsearch_log_group_name_variable(
    environment_parameter)
api_task_definition_family_variable = create_api_task_definition_family_variable(
    environment_parameter)
queue_worker_task_definition_family_variable = create_queue_worker_task_definition_family_variable(
    environment_parameter)
scheduler_task_definition_family_variable = create_scheduler_task_definition_family_variable(
    environment_parameter)
api_user_name_variable = create_api_user_name_variable(environment_parameter)
ci_user_name_variable = create_ci_user_name_variable(environment_parameter)
elasticsearch_domain_name_variable = create_elasticsearch_domain_name_variable(
    environment_parameter)
elasticsearch_log_access_policy_lambda_name_variable = create_elasticsearch_log_access_policy_lambda_name_variable(
    environment_parameter)

# Resources.

# Security Groups
load_balancer_security_group_resource = create_load_balancer_security_group_resource(
    template)
api_security_group_resource = create_api_security_group_resource(
    template, load_balancer_security_group_resource)
database_security_group_resource = create_database_security_group_resource(
    template, api_security_group_resource)
redis_security_group_resource = create_redis_security_group_resource(
    template, api_security_group_resource)
elasticsearch_security_group_resource = create_elasticsearch_security_group_resource(
    template, api_security_group_resource)

# Database
database_subnet_group_resource = create_database_subnet_group_resource(
    template, subnets_parameter)
database_resource = create_database_resource(template, database_allocated_storage_parameter,
                                             database_class_parameter, database_username_parameter,
                                             database_password_parameter, database_security_group_resource,
                                             database_subnet_group_resource)

# Redis
redis_subnet_group_resource = create_redis_subnet_group_resource(
    template, subnets_parameter)
redis_resource = create_redis_resource(template, redis_node_class_parameter, redis_nodes_count_parameter,
                                       redis_security_group_resource, redis_subnet_group_resource)

# Queues
default_queue_resource = create_default_queue_resource(
    template, default_queue_name_variable)
notifications_queue_resource = create_notifications_queue_resource(
    template, notifications_queue_name_variable)
search_queue_resource = create_search_queue_resource(
    template, search_queue_name_variable)

# Buckets
uploads_bucket_resource = create_uploads_bucket_resource(
    template, uploads_bucket_name_variable)

# Elastic Container Service
ecs_cluster_role_resource = create_ecs_cluster_role_resource(template)
ecs_cluster_resource = create_ecs_cluster_resource(template)

# EC2
ec2_instance_profile_resource = create_ec2_instance_profile_resource(
    template, ecs_cluster_role_resource)

# Launch Template
launch_template_resource = create_launch_template_resource(template, api_launch_template_name_variable,
                                                           api_instance_class_parameter, ec2_instance_profile_resource,
                                                           api_security_group_resource, ecs_cluster_resource)

# Docker
docker_repository_resource = create_docker_repository_resource(
    template, docker_repository_name_variable)

# Logs
api_log_group_resource = create_api_log_group_resource(
    template, api_log_group_name_variable)
queue_worker_log_group_resource = create_queue_worker_log_group_resource(
    template, queue_worker_log_group_name_variable)
scheduler_log_group_resource = create_scheduler_log_group_resource(
    template, scheduler_log_group_name_variable)
elasticsearch_log_group_resource = create_elasticsearch_log_group_resource(
    template, elasticsearch_log_group_name_variable)
elasticsearch_lambda_log_group_resource = create_elasticsearch_lambda_log_group_resource(
    template, elasticsearch_log_access_policy_lambda_name_variable)

# ECS Task Definitions
api_task_definition_resource = create_api_task_definition_resource(template, api_task_definition_family_variable,
                                                                   docker_repository_resource, api_log_group_resource)
queue_worker_task_definition_resource = create_queue_worker_task_definition_resource(template,
                                                                                     queue_worker_task_definition_family_variable,
                                                                                     docker_repository_resource,
                                                                                     queue_worker_log_group_resource,
                                                                                     default_queue_name_variable,
                                                                                     notifications_queue_name_variable,
                                                                                     search_queue_name_variable)
scheduler_task_definition_resource = create_scheduler_task_definition_resource(template,
                                                                               scheduler_task_definition_family_variable,
                                                                               docker_repository_name_variable,
                                                                               scheduler_log_group_resource)

# Load Balancing
load_balancer_resource = create_load_balancer_resource(
    template, load_balancer_security_group_resource, subnets_parameter)
api_target_group_resource = create_api_target_group_resource(
    template, vpc_parameter, load_balancer_resource)
load_balancer_listener_resource = create_load_balancer_listener_resource(template, load_balancer_resource,
                                                                         api_target_group_resource,
                                                                         certificate_arn_parameter)

# ECS Services
ecs_service_role_resource = create_ecs_service_role_resource(template)
api_service_resource = create_api_service_resource(template, ecs_cluster_resource, api_task_definition_resource,
                                                   api_task_count_parameter, api_target_group_resource,
                                                   ecs_service_role_resource, load_balancer_listener_resource)
queue_worker_service_resource = create_queue_worker_service_resource(template, ecs_cluster_resource,
                                                                     queue_worker_task_definition_resource,
                                                                     queue_worker_task_count_parameter)
scheduler_service_resource = create_scheduler_service_resource(template, ecs_cluster_resource,
                                                               scheduler_task_definition_resource,
                                                               scheduler_task_count_parameter)

# Auto Scaling
autoscaling_group_resource = create_autoscaling_group_resource(template, api_instance_count_parameter,
                                                               launch_template_resource)

# Users
api_user_resource = create_api_user_resource(template, api_user_name_variable, uploads_bucket_resource,
                                             default_queue_resource, notifications_queue_resource, search_queue_resource)
ci_user_resource = create_ci_user_resource(template, ci_user_name_variable)

# Elastic Search
elasticsearch_lambda_execution_role_resource = create_elasticsearch_lambda_execution_role_resource(
    template, elasticsearch_log_access_policy_lambda_name_variable, elasticsearch_log_group_name_variable)
elasticsearch_lambda_log_group_policy_function_resource = create_elasticsearch_lambda_log_group_policy_function_resource(
    template, elasticsearch_log_access_policy_lambda_name_variable, elasticsearch_lambda_log_group_resource, elasticsearch_lambda_execution_role_resource)
elasticsearch_log_group_policy_custom_resource = create_elasticsearch_log_group_policy_custom_resource(
    template, elasticsearch_lambda_log_group_policy_function_resource, elasticsearch_log_group_name_variable, elasticsearch_log_group_resource)
elasticsearch_service_linked_role_resource = create_elasticsearch_service_linked_role_resource(template)
elasticsearch_resource = create_elasticsearch_resource(template, elasticsearch_domain_name_variable,
                                                       elasticsearch_instance_count_parameter,
                                                       elasticsearch_instance_class_parameter,
                                                       elasticsearch_security_group_resource,
                                                       subnets_parameter)

# Outputs.
create_database_name_output(template, database_username_parameter)
create_database_username_output(template, database_username_parameter)
create_database_host_output(template, database_resource)
create_database_port_output(template, database_resource)
create_redis_host_output(template, redis_resource)
create_redis_port_output(template, redis_resource)
create_default_queue_url_output(template, default_queue_resource)
create_default_queue_output(template, default_queue_name_variable)
create_notifications_queue_output(template, notifications_queue_name_variable)
create_load_balancer_domain_output(template, load_balancer_resource)
create_elasticsearch_service_linked_role_id_output(template, elasticsearch_service_linked_role_resource)
create_elasticsearch_host_output(template, elasticsearch_resource)
create_docker_repository_uri_output(template, docker_repository_resource)
create_docker_cluster_name_output(template, ecs_cluster_resource)
create_uploads_bucket_name_output(template, uploads_bucket_name_variable)

# Troposhere cannot set LogPublishingOptions on Elasticsearch, so we do it via JSON here
json_template = json.loads(template.to_json())
json_template["Resources"]["Elasticsearch"]["Properties"]["LogPublishingOptions"] = {
    "ES_APPLICATION_LOGS": {
        "CloudWatchLogsLogGroupArn": {
            "Fn::GetAtt": [
                "SearchLogGroup",
                "Arn"
            ]
        },
        "Enabled": True
    }
}

# Print the generated template in JSON.
print(json.dumps(json_template, indent=4))
