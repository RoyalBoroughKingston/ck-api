#!/usr/bin/env bash

# ================================
# Manage objects in a AWS S3 Bucket
# This script will install the AWS CLI
# If you don't want this on your system, install using the docker helper script.
# ================================

# Can accept the following environment variables
# $AWS_ACCESS_KEY_ID = AWS IAM user Access Key ID.
# $AWS_SECRET_ACCESS_KEY = AWS IAM user Access Key Secret.
# $AWS_DEFAULT_REGION = The region the bucket is in, e.g. eu-west-2.
# $AWS_BUCKET_NAME = The name of the AWS S3 bucket to use

# Bail out on first error.
set -e

# Set environment variables.
APPROOT=${APPROOT:-'/var/www/html'}
RED='\e[1;31m'
BLUE='\e[1;34m'
GREEN='\e[1;32m'
ENDCOLOUR='\e[1;m'

# Get the AWS details
if [ -z "$AWS_ACCESS_KEY_ID" ]; then
    read -p 'AWS Access Key ID: ' AWS_ACCESS_KEY_ID
fi

if [ -z "$AWS_SECRET_ACCESS_KEY" ]; then
    read -sp 'AWS Secret Access Key: ' AWS_SECRET_ACCESS_KEY
    echo
fi

if [ -z "$AWS_DEFAULT_REGION" ]; then
    read -p 'AWS Default Region: ' AWS_DEFAULT_REGION
fi

if [ -z "$AWS_BUCKET_NAME" ]; then
    read -p 'AWS Bucket Name: ' AWS_BUCKET_NAME
fi

# Install required packages
apt-get update && apt-get install -y --allow-unauthenticated wget jq unzip less

# Install AWS CLI
echo -e "${BLUE}Installing AWS CLI...${ENDCOLOUR}"
rm -Rf ${PWD}/tmp
mkdir tmp
cd tmp
wget -q -O awscliv2.zip https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip
unzip awscliv2.zip
${PWD}/aws/install
aws --version
rm  awscliv2.zip
cd ../

# Select what operation to perform
read -p '(L)ist, (G)et, Get (A)ll, (U)pload all, (P)ut or (D)elete an object: ' ACTION
case $ACTION in
    "L"|"l"|"G"|"g"|"A"|"a"|"U"|"u"|"P"|"p"|"D"|"d")
    ;;
    *)
    echo -e "${RED}The action should be one of (L)ist, (G)et, Get (A)ll, (U)pload all, (P)ut or (D)elete${ENDCOLOUR}"
    exit
    ;;
esac

if [ "$ACTION" == 'L' ] || [ "$ACTION" == 'l' ]; then
# List the bucket contents
    echo -e "${GREEN}The contents of bucket $AWS_BUCKET_NAME are:${ENDCOLOUR}"
    echo `aws s3api list-objects --bucket ${AWS_BUCKET_NAME}` | jq >>  "${AWS_BUCKET_NAME}.json"
fi

if [ "$ACTION" == 'G' ] || [ "$ACTION" == 'g' ]; then
    # Download a bucket object
    read -p 'What is the key of the object to download?' OBJECT_KEY
    echo "Downloading $OBJECT_KEY from bucket $AWS_BUCKET_NAME to ${PWD}/${OBJECT_KEY}"
    aws s3api get-object --bucket ${AWS_BUCKET_NAME} --key ${OBJECT_KEY} ${PWD}/${OBJECT_KEY}
fi

if [ "$ACTION" == 'A' ] || [ "$ACTION" == 'a' ]; then
    # Download all bucket objects
    echo "Downloading all objects from bucket $AWS_BUCKET_NAME to ${PWD}/${AWS_BUCKET_NAME}"
    aws s3 sync "s3://${AWS_BUCKET_NAME}" ${PWD}/${AWS_BUCKET_NAME}
fi

if [ "$ACTION" == 'U' ] || [ "$ACTION" == 'u' ]; then
    # Upload all files from a directory to a bucket
    read -p 'What is the path to the directory? relative to the application root (e.g. my_directory, storage/cloud/files/public)' FILE_DIRECTORY

    if [ ! -e "$APPROOT/$FILE_DIRECTORY" ]; then
        echo -e "${RED}The directory does not exist${ENDCOLOUR}"
        exit
    fi

    read -p "Sync all files from ${APPROOT}/$FILE_DIRECTORY to bucket $AWS_BUCKET_NAME Proceed? (Y/n): " PROCEED

    PROCEED=${PROCEED:-'Y'}

    if [ "$PROCEED" != 'Y' ] && [ "$PROCEED" != 'y' ]; then
        echo -e "${RED}Aborting directory sync${ENDCOLOUR}"
        exit
    fi

    echo "Syncing $APPROOT/$FILE_DIRECTORY to bucket $AWS_BUCKET_NAME"

    aws s3 sync ${APPROOT}/$FILE_DIRECTORY "s3://${AWS_BUCKET_NAME}"
fi

if [ "$ACTION" == 'P' ] || [ "$ACTION" == 'p' ]; then
    # Get the upload details
    read -p 'Which environment is to be updated? (staging or production): ' ENVIRONMENT

    if [ "$ENVIRONMENT" != 'staging' ] && [ "$ENVIRONMENT" != 'production' ]; then
        echo -e "${RED}The environment should be one of staging or production${ENDCOLOUR}"
        exit
    fi

    echo 'What is the path to the file? relative to the application root (e.g. .env, storage/cloud/files/public/...)'

    read FILE_PATH

    if [ ! -e "$APPROOT/$FILE_PATH" ]; then
        echo -e "${RED}The file does not exist${ENDCOLOUR}"
        exit
    fi

    if [[ $FILE_PATH == *".env"* ]]; then
        FILE_KEY=".env.frontend.${ENVIRONMENT}"
    else
        read -p 'What is the path this file should be stored as? (e.g. files/public/abc123.png): ' FILE_KEY
    fi

    if [ -z "$FILE_KEY" ]; then
        echo -e "${RED}The file does not match the type of file this script is for${ENDCOLOUR}"
        exit
    fi

    # Check the user is happy to store the proposed update
    read -p "Storing $FILE_PATH as $FILE_KEY Proceed? (Y/n): " PROCEED

    PROCEED=${PROCEED:-'Y'}

    if [ "$PROCEED" != 'Y' ] && [ "$PROCEED" != 'y' ]; then
        echo -e "${RED}Aborting file storage${ENDCOLOUR}"
        exit
    fi

    echo "Uploading $APPROOT/$FILE_PATH to bucket $AWS_BUCKET_NAME as object $FILE_KEY"

    aws s3api put-object --bucket ${AWS_BUCKET_NAME} --key "$FILE_KEY" --body "$APPROOT/$FILE_PATH"

fi

if [ "$ACTION" == 'D' ] || [ "$ACTION" == 'd' ]; then
    # Delete a bucket object
    read -p 'What is the key of the object to delete: ' OBJECT_KEY
    # Check the user is happy to delete the object
    read -p "Deleting $OBJECT_KEY from bucket $AWS_BUCKET_NAME Proceed? (Y/n): " PROCEED

    PROCEED=${PROCEED:-'Y'}

    if [ "$PROCEED" != 'Y' ] && [ "$PROCEED" != 'y' ]; then
        echo -e "${RED}Aborting object delete${ENDCOLOUR}"
        exit
    fi
    aws s3api delete-object --bucket ${AWS_BUCKET_NAME} --key ${OBJECT_KEY}
fi

# Remove the AWS client
rm -Rf ${PWD}/tmp
