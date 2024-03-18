# About
This is a simple way to use Amazon Web Services (AWS) for dynamic DNS (DynDNS) updates.

# Tech Stack
This application is written in PHP and comes with a Docker setup.

## Composer
To install Composer packages via Docker use the following command:
```
docker run --rm --interactive --tty --volume $PWD:/app --user $(id -u):$(id -g) composer install --ignore-platform-reqs
```

## Laravel Pint
Run Laravel Pint for code styling when Docker containers are running:
```
docker exec aws-dyndns-php-1 ./vendor/bin/pint
```
