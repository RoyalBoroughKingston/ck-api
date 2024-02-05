# Set base image.
FROM node:16.20

# Set maintainer to Ayup Digital.
LABEL maintainer="Ayup Digital"

# Install git for faster package downloads.
RUN apt-get install -y git

# Create new cache directory for npm
# See https://stackoverflow.com/questions/77626859/npm-cannot-run-dev-in-docker-node-container-due-to-root-owned-files
RUN mkdir -p /home/node/app/.npm \
&& chown -R node:node /home/node/app/.npm

ENV npm_config_cache /home/node/app/.npm

# Set the working directory to the project root.
WORKDIR /var/www/html
