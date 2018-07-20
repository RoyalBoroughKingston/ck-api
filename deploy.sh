#!/usr/bin/env bash

# Set variables.
CLI_URL=https://cli.run.pivotal.io/stable?release=linux64binary&source=github
API=api.cloud.service.gov.uk
ORGANISATION=kingston-council-digital-design
SPACE=connectwellkingston-dev
SECRETS_FILE=secrets.travis.yml

# Set missing variables.
php -r "
    \$contents = file_get_contents('$SECRETS_FILE');
    \$contents = strtr(\$contents, [
        'APP_KEY:' => 'APP_KEY: '.getenv('APP_KEY'),
        'DB_DATABASE:' => 'DB_DATABASE: '.getenv('DB_DATABASE'),
        'DB_HOST:' => 'DB_HOST: '.getenv('DB_HOST'),
        'DB_PASSWORD:' => 'DB_PASSWORD: '.getenv('DB_PASSWORD'),
        'DB_USERNAME:' => 'DB_USERNAME: '.getenv('DB_USERNAME'),
        'REDIS_HOST:' => 'REDIS_HOST: '.getenv('REDIS_HOST'),
        'REDIS_PASSWORD:' => 'REDIS_PASSWORD: '.getenv('REDIS_PASSWORD'),
    ]);
    file_put_contents('$SECRETS_FILE', \$contents);"

# Download Cloud Foundry CLI.
curl -L "$CLI_URL" | tar -zx

# Connect to the Cloud Foundry API.
./cf api $API

# Login to Cloud Foundry.
./cf login -u $CF_USERNAME -p $CF_PASSWORD -o $ORGANISATION -s $SPACE

# Deploy
./cf push --vars-file $SECRETS_FILE
