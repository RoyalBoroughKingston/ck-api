# Connected Kingston

A scheme run by RBK to help residents take control of their own health by connecting them with services and events in their local area.
This system forms the online aspect of this scheme, by providing an API as well a frontend and backend web app.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. 
See deployment for notes on how to deploy the project on a live system.

### Prerequisites

* PHP
* Composer
* Vagrant
* Docker

### Installing

Start by cloning the example configuration files and configuring as needed. For more information about configuring the 
`Homestead.yaml` file, please consult the [Laravel Docs](https://laravel.com/docs/5.6/homestead):

```bash
cp Homestead.yaml.example Homestead.yaml
cp .env.example .env

# Installs Laravel Homestead.
composer install --ignore-platform-reqs

# Update your hosts file.
sudo vim /etc/hosts

# Append this to the end (use the IP and hostname set in Homestead.yaml).
192.168.10.12 api.connectedkingston.test
```

You should then be able to start the VM and SSH into it:

```bash
vagrant up

# Allows access to Elasticsearch on host machine
vagrant ssh -- -R 9200:localhost:9200

cd ck-api

# Generate the application key.
php artisan key:generate

# Run the migrations.
php artisan migrate

# Install the OAuth 2.0 keys.
php artisan passport:keys

# Create the first Global Admin user (take a note of the password outputted).
php artisan ck:create-user <first-name> <last-name> <email> <phone-number>
```

Ensure any API clients have been created:

```bash
php artisan passport:client

# Make sure to leave the user ID blank when creating.
```

In a separate terminal on your host machine, pull the Elasticsearch docker image and start an instance of it:

```bash
docker pull docker.elastic.co/elasticsearch/elasticsearch:6.3.2
docker run -p 9200:9200 -p 9300:9300 -e "discovery.type=single-node" docker.elastic.co/elasticsearch/elasticsearch:6.3.2
```

Then back on the homestead VM, run the seeder and setup the Elasticsearch index:

```bash
# Create the index.
php artisan elastic:create-index App\\Models\\IndexConfigurators\\ServicesIndexConfigurator

# Set the fields mappings for the index.
php artisan elastic:update-mapping App\\Models\\Service

# Run the seeder.
php artisan db:seed

# Import all of the services from the database into the index.
php artisan scout:import App\\Models\\Service
```

Finally, install the NPM dependencies and compile all assets:

```bash
npm install
npm run dev
```

## Running the tests

To run the PHPUnit tests:
 
```bash
php vendor/bin/phpunit
```

To run the code style tests:

```bash
php vendor/bin/phpcs
```

## Deployment

Deployment is fully automated by pushing a commit to `develop` or `master`. More information on this process can be [found in the wiki](https://github.com/RoyalBoroughKingston/ck-api/wiki/Branching-and-Release-Strategy#continuous-delivery).

## Built With

* [Laravel](https://laravel.com/docs/) - The Web Framework Used
* [Composer](https://getcomposer.org/doc/) - Dependency Management

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/RoyalBoroughKingston/ck-api/tags). 

## Authors

* [Ayup Digital](https://ayup.agency/)

See also the list of [contributors](https://github.com/RoyalBoroughKingston/ck-api/contributors) who participated in this project.

## License

This project is licensed under the GNU AGPLv3 License - see the [LICENSE.md](LICENSE.md) file for details.
