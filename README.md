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

## AWS Policy
To be able to manage DNS records for dynamic DNS in Route 53 at the Amazon Web Services credentials
have to be provided to this application. To do so you can create a new user in an existing AWS account
and generate authentication codes (= key and secret) for the user. You also have to create a policy
which allows to manage DNS records for the relevant hosted zone ("domain") in AWS Route 53. This policy
then has to be assigned to the newly created user account. You can use the given policy below, just make
sure to replace `<your-hosted-zone-id>` with the correct id of your hosted zone:
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": "route53:ChangeResourceRecordSets",
            "Resource": "arn:aws:route53:::hostedzone/<your-hosted-zone-id>"
        }
    ]
}
```
