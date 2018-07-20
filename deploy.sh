#!/usr/bin/env bash

# Set variables.
SECRETS_FILE="secrets.travis.yml"

# Set missing variables.
php artisan cwk:set-cf-secrets --path=$SECRETS_FILE

# Connect to the Cloud Foundry API.
cf api $CF_API

# Login to Cloud Foundry.
cf login -u $CF_USERNAME -p $CF_PASSWORD -o $CF_ORGANISATION -s $CF_SPACE

# Deploy
cf push --vars-file $SECRETS_FILE
