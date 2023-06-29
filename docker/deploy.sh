#!/usr/bin/env bash

# Requires the following environment variables:
# $REPO_URI = The URI of the Docker repo to push to.
# $CLUSTER = The name of the ECS cluster.
# $SERVICE = The name of the ECS service.
# $AWS_ACCESS_KEY_ID = The AWS access key.
# $AWS_SECRET_ACCESS_KEY = The AWS secret access key.
# $AWS_DEFAULT_REGION = The AWS region.

# Bail out on first error.
set -e

# Set the deploy variables
OLD_IFS=$IFS
IFS='/'
read AWS_DOCKER_REGISTRY AWS_DOCKER_REPO <<< "${REPO_URI}"
IFS=$OLD_IFS
export AWS_DOCKER_REGISTRY=${AWS_DOCKER_REGISTRY}
export AWS_DOCKER_REPO=${AWS_DOCKER_REPO}

# Login to the ECR.
echo "Logging in to ECR docker registry: $AWS_DOCKER_REGISTRY in region $AWS_DEFAULT_REGION"
aws ecr get-login-password --region ${AWS_DEFAULT_REGION} | docker login --username AWS --password-stdin ${AWS_DOCKER_REGISTRY}

# Push the Docker image to ECR.
echo "Pushing images to ECR..."
docker push ${REPO_URI}:latest
# docker push ${REPO_URI}:${TRAVIS_COMMIT}

# Update the service.
echo "Updating the ECS service..."
aws ecs update-service \
    --cluster ${CLUSTER} \
    --service ${SERVICE} \
    --force-new-deployment
