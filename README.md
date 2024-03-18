# About
This is a simple way to use Amazon Web Services (AWS) for dynamic DNS (DynDNS) updates.

# Tech Stack
| Technology    | Info                                                                                  |
|---------------|---------------------------------------------------------------------------------------|
| AWS Route 53  | Route 53 in the Amazon Web Services is used as backend to manage dynamic DNS records. |
| AWS SDK (PHP) | The AWS SDK for PHP is used to interact with the AWS Route 53 API.                    |
| Composer      | Composer is used to manage PHP packages/dependencies and for autoloading.             |
| Docker        | Docker (compose) is used to run this application for development and production.      |
| Laravel Pint  | Pint is used to have a consistent code style.                                         |
| PHP           | PHP is used as programming language.                                                  |
| Traefik       | Traefik is used as reverse proxy. It manages Lets Encrypt certificates in production. |

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
