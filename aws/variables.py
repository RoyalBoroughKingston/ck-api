from troposphere import Join, Ref


def create_default_queue_name_variable(environment_parameter, uuid_parameter):
    return Join('-', [Ref(environment_parameter), Ref(uuid_parameter), 'default'])


def create_notifications_queue_name_variable(environment_parameter, uuid_parameter):
    return Join('-', [Ref(environment_parameter), Ref(uuid_parameter), 'notifications'])


def create_search_queue_name_variable(environment_parameter, uuid_parameter):
    return Join('-', [Ref(environment_parameter), Ref(uuid_parameter), 'search'])


def create_uploads_bucket_name_variable(environment_parameter, uuid_parameter):
    return Join('-', ['uploads', Ref(environment_parameter), Ref(uuid_parameter)])


def create_api_launch_template_name_variable(environment_parameter):
    return Join('-', ['api-launch-template', Ref(environment_parameter)])


def create_docker_repository_name_variable(environment_parameter, uuid_parameter):
    return Join('-', ['api', Ref(environment_parameter), Ref(uuid_parameter)])


def create_api_log_group_name_variable(environment_parameter):
    return Join('-', ['api', Ref(environment_parameter)])


def create_queue_worker_log_group_name_variable(environment_parameter):
    return Join('-', ['queue-worker', Ref(environment_parameter)])


def create_scheduler_log_group_name_variable(environment_parameter):
    return Join('-', ['scheduler', Ref(environment_parameter)])


def create_elasticsearch_log_group_name_variable(environment_parameter):
    return Join('-', ['search', Ref(environment_parameter)])


def create_api_task_definition_family_variable(environment_parameter):
    return Join('-', ['api', Ref(environment_parameter)])


def create_queue_worker_task_definition_family_variable(environment_parameter):
    return Join('-', ['queue-worker', Ref(environment_parameter)])


def create_scheduler_task_definition_family_variable(environment_parameter):
    return Join('-', ['scheduler', Ref(environment_parameter)])


def create_api_user_name_variable(environment_parameter):
    return Join('-', ['api', Ref(environment_parameter)])


def create_ci_user_name_variable(environment_parameter):
    return Join('-', ['ci-api', Ref(environment_parameter)])


def create_api_name_variable():
    return 'Sutton Information Hub'


def create_elasticsearch_domain_name_variable(environment_parameter):
    return Join('-', ['search', Ref(environment_parameter)])


def create_elasticsearch_log_access_policy_lambda_name_variable(environment_parameter):
    return Join('-', ['search', 'lambda', Ref(environment_parameter)])
