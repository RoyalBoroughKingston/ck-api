#!/usr/bin/env bash

# Set variables.
CLI_URL=https://cli.run.pivotal.io/stable?release=linux64binary&source=github
SECRETS_FILE=secrets.travis.yml

# Set missing variables.
php -r "
    \$contents = file_get_contents('$SECRETS_FILE');
    \$contents = strtr(\$contents, [
        'APP_KEY:' => 'APP_KEY: '.getenv('CF_APP_KEY'),
        'DB_DATABASE:' => 'DB_DATABASE: '.getenv('CF_DB_DATABASE'),
        'DB_HOST:' => 'DB_HOST: '.getenv('CF_DB_HOST'),
        'DB_PASSWORD:' => 'DB_PASSWORD: '.getenv('CF_DB_PASSWORD'),
        'DB_USERNAME:' => 'DB_USERNAME: '.getenv('CF_DB_USERNAME'),
        'REDIS_HOST:' => 'REDIS_HOST: '.getenv('CF_REDIS_HOST'),
        'REDIS_PASSWORD:' => 'REDIS_PASSWORD: '.getenv('CF_REDIS_PASSWORD'),
    ]);
    file_put_contents('$SECRETS_FILE', \$contents);"

# Download Cloud Foundry CLI.
curl -L "$CLI_URL" | tar -zx

# Connect to the Cloud Foundry API.
./cf api $CF_API

# Login to Cloud Foundry.
./cf login -u $CF_USERNAME -p $CF_PASSWORD -o $CF_ORGANISATION -s $CF_SPACE

# Deploy
./cf push --vars-file $SECRETS_FILE
