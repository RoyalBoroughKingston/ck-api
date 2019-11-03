# Connected Kingston

A scheme run by RBK to help residents take control of their own health 
by connecting them with services and events in their local area. This 
system forms the online aspect of this scheme, by providing an API as 
well a frontend and backend web app.

## Getting Started

These instructions will get you a copy of the project up and running on 
your local machine for development and testing purposes. See deployment 
for notes on how to deploy the project on a live system.

### Prerequisites

* [Docker](https://docker.com/)

### Installing

Start by spinning up the docker containers using the convenience script:

```bash
# Once copied, edit this file to configure as needed.
cp .env.example .env

# Spin up the docker containers and detach so they run the background.
./develop up -d

# Install dependencies.
./develop composer install
./develop npm install

# Compile static assets.
./develop npm run dev
```

> After starting the Docker containers as described above, you should
wait a minute or two before progressing to the next steps. This is due
to the MySQL and Elasticsearch containers taking a few minutes to fully
boot up.

You should then be able to run the setup commands using the convenience 
script:

```bash
# Generate the application key.
./develop artisan key:generate

# Run the migrations.
./develop artisan migrate

# Install the OAuth 2.0 keys.
./develop artisan passport:keys

# Create the first Global Admin user (take a note of the password outputted).
./develop artisan ck:create-user <first-name> <last-name> <email> <phone-number>

# Create the OAuth client for the admin app (and any other clients).
./develop artisan ck:create-oauth-client <name> <redirect-uri> [--first-party]

# Create/update the Elasticsearch index.
./develop artisan ck:reindex-elasticsearch
```

## Running the tests

To run all tests:
 
```bash
./develop composer test
```

To run only the PHPUnit tests:
 
```bash
./develop composer test:unit
```

To run only the code style tests:

```bash
# Run linter.
./develop composer test:style

# Fix linting errors.
./develop composer fix:style
```

## Deployment

Deployment is fully automated by pushing a commit to `develop` or 
`master`. More information on this process can be [found in the wiki](https://github.com/RoyalBoroughKingston/ck-api/wiki/Branching-and-Release-Strategy#continuous-delivery).

## Built with

* [Laravel](https://laravel.com/docs/) - The Web Framework Used
* [Composer](https://getcomposer.org/doc/) - Dependency Management
* [Docker](https://www.docker.com/) - Containerisation

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code 
of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions 
available, see the [tags on this repository](https://github.com/RoyalBoroughKingston/ck-api/tags). 

## Authors

* [Ayup Digital](https://ayup.agency/)

See also the list of [contributors](https://github.com/RoyalBoroughKingston/ck-api/contributors) 
who participated in this project.

## License

This project is licensed under the GNU AGPLv3 License - see the 
[LICENSE.md](LICENSE.md) file for details.
