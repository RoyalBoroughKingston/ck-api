# ==================================================
# This stack creates the API infrastructure.
# ==================================================
from troposphere import Template, Parameter, Ref, GetAtt, Join, Output
import troposphere.s3 as s3
import troposphere.iam as iam
import troposphere.sqs as sqs
import uuid

# ==================================================
# Template details.
# ==================================================
template = Template('Create the infrastructure needed to run the Connected Kingston API')
template.add_version('2010-09-09')

# ==================================================
# Parameters.
# ==================================================
uuid_parameter = template.add_parameter(
  Parameter(
    'Uuid',
    Type='String',
    Default=str(uuid.uuid4()),
    Description='The unique ID for this stack.',
    MinLength='36',
    MaxLength='36'
  )
)

environment_parameter = template.add_parameter(
  Parameter(
    'Environment',
    Type='String',
    Description='The environment this stack is for (e.g. production or staging).',
    MinLength='1'
  )
)

# ==================================================
# Variables.
# ==================================================
bucket_name_variable = Join('-', ['api', Ref(environment_parameter), Ref(uuid_parameter)])
default_queue_name_variable = Join('-', [Ref(environment_parameter), 'default'])
notifications_queue_name_variable = Join('-', [Ref(environment_parameter), 'notifications'])
search_queue_name_variable = Join('-', [Ref(environment_parameter), 'search'])
ci_user_name_variable = Join('-', ['ci-api', Ref(environment_parameter)])
api_user_name_variable = Join('-', ['api', Ref(environment_parameter)])

# ==================================================
# Resources.
# ==================================================
bucket_resource = template.add_resource(
  s3.Bucket(
    'Bucket',
    AccessControl='Private',
    BucketEncryption=s3.BucketEncryption(
      ServerSideEncryptionConfiguration=[
        s3.ServerSideEncryptionRule(
          ServerSideEncryptionByDefault=s3.ServerSideEncryptionByDefault(
            SSEAlgorithm='AES256'
          )
        )
      ]
    ),
    BucketName=bucket_name_variable,
    VersioningConfiguration=s3.VersioningConfiguration(
      Status='Enabled'
    )
  )
)

default_queue_resource = template.add_resource(
    sqs.Queue(
        'DefaultQueue',
        QueueName=default_queue_name_variable
    )
)

notifications_queue_resource = template.add_resource(
    sqs.Queue(
        'NotificationsQueue',
        QueueName=notifications_queue_name_variable
    )
)

search_queue_resource = template.add_resource(
    sqs.Queue(
        'SearchQueue',
        QueueName=search_queue_name_variable
    )
)

ci_user_resource = template.add_resource(
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

api_user_resource = template.add_resource(
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
              'Resource': '*'
            },
            {
              'Action': 'sqs:*',
              'Effect': 'Allow',
              'Resource': [
                GetAtt(default_queue_resource, 'Arn'),
                GetAtt(notifications_queue_resource, 'Arn'),
                GetAtt(search_queue_resource, 'Arn')
              ]
            }
          ]
        }
      )
    ]
  )
)

# ==================================================
# Outputs.
# ==================================================
template.add_output(
  Output(
    'BucketName',
    Description='The S3 bucket name',
    Value=bucket_name_variable
  )
)

template.add_output(
  Output(
    'DefaultQueue',
    Description='The URI of the default queue',
    Value=Ref(default_queue_resource)
  )
)

template.add_output(
  Output(
    'NotificationsQueue',
    Description='The URI of the notifications queue',
    Value=Ref(notifications_queue_resource)
  )
)

template.add_output(
  Output(
    'SearchQueue',
    Description='The URI of the search queue',
    Value=Ref(search_queue_resource)
  )
)

# ==================================================
# Print the generated template in JSON.
# ==================================================
print(template.to_json())
