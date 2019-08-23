# Set base image.
FROM python:3.7-slim

# Set maintainer to Ayup Digital.
LABEL maintainer="Ayup Digital"

# Set the working directory to the project root.
WORKDIR /tmp

# Install generic software.
RUN apt-get -qq update \
    && apt-get install -y build-essential libssl-dev groff \
    && rm -rf /var/lib/apt/lists/*

# Copy and install the Python dependency requirements.
COPY requirements.txt .
RUN pip install -r requirements.txt

# Set default command.
CMD ["pip", "freeze"]
