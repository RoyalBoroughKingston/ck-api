#!/usr/bin/env bash

# Requires the following environment variables:
# $TRAVIS_BRANCH = The branch the build is against.
# $CF_API = The URI of the Cloud Foundry instance.
# $CF_USERNAME = The Cloud Foundry username.
# $CF_PASSWORD = The Cloud Foundry password.
# $CF_ORGANISATION = The Cloud Foundry organisation.
# $CF_SPACE = The Cloud Foundry space.

# Bail out on first error.
set -e

# Get the environment from the branch.
case ${TRAVIS_BRANCH} in
    master )
        ENVIRONMENT=production
        ;;
    develop )
        ENVIRONMENT=staging
        ;;
esac

# Declare the configuration variables for the deployment.
echo "Setting deployment configuration for ${ENVIRONMENT}..."
ENV_SECRET_ID=".env.api.${ENVIRONMENT}"
PUBLIC_KEY_SECRET_ID="oauth-public.key.${ENVIRONMENT}"
PRIVATE_KEY_SECRET_ID="oauth-private.key.${ENVIRONMENT}"
SECRETS_FILE=".travis/secrets.${ENVIRONMENT}.yml"

# Get the .env file.
echo "Downloading .env file..."
rm -f .env
aws secretsmanager get-secret-value \
    --secret-id ${ENV_SECRET_ID} | \
    python -c "import json,sys;obj=json.load(sys.stdin);print obj['SecretString'];" > .env

# Get the OAuth keys.
echo "Downloading public OAuth key..."
rm -f storage/oauth-public.key
aws secretsmanager get-secret-value \
    --secret-id ${PUBLIC_KEY_SECRET_ID} | \
    python -c "import json,sys;obj=json.load(sys.stdin);print obj['SecretString'];" > storage/oauth-public.key

echo "Downloading private OAuth key..."
rm -f storage/oauth-private.key
aws secretsmanager get-secret-value \
    --secret-id ${PRIVATE_KEY_SECRET_ID} | \
    python -c "import json,sys;obj=json.load(sys.stdin);print obj['SecretString'];" > storage/oauth-private.key

# Connect to the Cloud Foundry API.
echo "Logging into Cloud Foundry..."
cf api $CF_API

# Login to Cloud Foundry.
cf login -u $CF_USERNAME -p $CF_PASSWORD -o $CF_ORGANISATION -s $CF_SPACE

# Deploy.
cf push --vars-file "${SECRETS_FILE}"
