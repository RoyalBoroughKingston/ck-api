# Set base image.
FROM python:3.7-slim

# Set maintainer to Ayup Digital.
LABEL maintainer="Ayup Digital"

# Set the working directory to the project root.
WORKDIR /tmp

# Install generic software.
RUN pip install troposphere \
    awacs \
    pyyaml \
    yapf==0.11.0 \
    flake8 \
    autoflake \
    awscli \
    codeclimate-test-reporter \
    rainbow_logging_handler

# Copy and install the Python dependency requirements.
COPY requirements.txt .
RUN pip install -r requirements.txt

# Set default command.
CMD ["pip", "freeze"]
