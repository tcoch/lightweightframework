# LightWeightFramework

[![Tests](https://github.com/tcoch/lightweightframework/actions/workflows/php.yml/badge.svg)](https://github.com/tcoch/lightweightframework/actions/workflows/php.yml)
[![codecov](https://codecov.io/github/tcoch/lightweightframework/graph/badge.svg?token=768EU7ZI3Q)](https://codecov.io/github/tcoch/lightweightframework)

## The project

> [!WARNING]
> This project is **NOT** an actual framework to use in production.
> It is only meant to be used as educational purposes.

_This project was originally initiated by a use case: adding tests and code coverage
to a client project based solly on procedural PHP._

The objective is to build a fully functional and testable framework, from scratch. 
Some dependencies such as PHPUnit or PHPStan will be integrated, 
for tests purposes and to lean towards a minimal code quality.

Building from scratch will allow us to dive deep into the construction
and the logic.

### How it works

You'll find here some informations about the workflow of this framework.

- When a request to a route `/foo` is made, it is sent to the file `public/index.php`, 
which retrieve this user request and creates a response
- To create the response, a router is used, to match the requested path to a `Controller`
(most of them, in `src/Controller`, only perform)

<details>
<summary>Note on accessing PHP files by name</summary>

- If a PHP script / file exists in the `public` directory, it will be executed as-is.
The `public/index.php` will not be used as an entrypoint and the framework is therefore "bypassed".
- If it does not exist, it can be used as a route.

This is done to simplify migrating from a procedural project towards this framework.
Applicable configuration given below.

**_router.json:_**
```php
return [
    "/foo.php" => [new \App\Foo(), 'execute'],
    // foo.php is the request to match (http://localhost/foo.php)
    // \App\Foo::execute() is the method to actually call when request match this route
];
```

**_Dockerfile:_**
```dockerfile
RUN sed -i '/<\/VirtualHost>/i\
            <Directory /var/www/html/public>\n\
                    AllowOverride All\n\
                    Require all granted\n\
            </Directory>\n' /etc/apache2/sites-enabled/000-default.conf
```

**_.htaccess:_**
```apacheconf
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [L,QSA]
</IfModule>
```
</details>

### Containerizing

A `Dockerfile` and a `compose` file are provided in this repository.
They are voluntary simplified and use `php:8.4-apache` as a base image
in order to provide a fully customizable environment.

## Testing

> [!NOTE]
> Project testing should not include `#[Covers]` tags when testing
> (and trying to cover) procedural scripts.
> If you do, code coverage will not work on those scripts.

To run tests, execute :

```bash
username@localhost:~$ docker exec -it lightweightframework bash
root@lightweightframework:/var/www/html# vendor/bin/phpunit
```
