# About
This is a simple way to use Amazon Web Services (AWS) for dynamic DNS (DynDNS) updates.

# Running locally
If you want to start the application locally for testing or development you can do so easily.
At first, you have to define the application settings. To do so you can create a copy of the `.env.example`:
```
cp .env.example .env
```
Have a look at the created `.env` file and customize it as to your needs. Then use Docker to start the application:
```
docker compose up
```
Depending on your settings you should now be able to access the application with your web browser by calling a URL
similar to this:
```
http://localhost/?authToken=my-secret-token&awsDomainName=my-ip.example.com&ip=127.0.0.1
```

# Deploy to production
This production setup requires [Docker](https://www.docker.com/). You can run your own web server with Docker in minutes
using services like [DigitalOcean](https://www.digitalocean.com/) or [Vultr](https://www.vultr.com/). 
This production setup also depends on [docker-traefik](https://github.com/bst27/docker-traefik) running on the
production machine so make sure to first prepare it before continuing with this setup.

At first, you have to define the application settings. To do so you can create a copy of the `.env.example`:
```
cp .env.example .env
```
Have a look at the created `.env` file and customize it as to your needs. Especially make sure you have DNS records
for the specified hostname ("domain") so that Traefik will be able to automatically obtain a certificate after deployment.

To deploy into production `docker compose` is used. Make sure to create a docker context **once** before the first
execution. For this to work you need SSH access with SSH key authentication to the production machine. Replace
`<username>` and `<hostname>` and run the following command to create a docker context named `production`:
```
docker context create production --docker "host=ssh://<username>@<hostname>"
```

With the docker context created you can run the following command to deploy to the production machine:
```
docker context use production && \
docker compose -f docker-compose.production.yml build --no-cache && \
sleep 3s && \
docker compose -f docker-compose.production.yml stop && \
sleep 3s && \
docker compose -f docker-compose.production.yml up -d && \
sleep 3s && \
docker context use default
```

When this step is complete [docker-traefik](https://github.com/bst27/docker-traefik) should automatically generate a
Lets Encrypt certificate and start serving your application at the hostname previously defined in your `.env` file.
Depending on your settings you should now be able to dynamically update the DNS records of your domain by calling a
web URL similar to this:
```
https://aws-dyndns.example.com/?authToken=my-secret-token&awsDomainName=my-ip.example.com&ip=127.0.0.1
```
You should get one of the following HTTP response status codes:

| Status Code | Info                                                                                                                          |
|-------------|-------------------------------------------------------------------------------------------------------------------------------|
| 200         | OK: Everything worked as expected. DNS records have been updated successfully.                                                |
| 400         | Bad Request: Probably a required parameter is missing (see response details).                                                 |
| 403         | Forbidden: You defined an auth token in the application settings but the auth token given in the request is missing or wrong. |
| 429         | Too Many Requests: You defined a rate limit in the application settings and reached it for the current hour.                  |
| 502         | Bad Gateway: There has been an error in the interaction with the AWS Route 53 API.                                            |

If everything works you can use this URL to automatically update DNS records with your current IP, e.g. by setting it
up in your router at home. For details about how to do this you should have a look at the docs for your specific router.

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
