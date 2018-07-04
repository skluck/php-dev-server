# PHP Dev Server Setup

It is very common to develop only against the local PHP built-in web server:
```
php -S localhost:8080 -t public
```

However, this set up is typically wildly different than you would use in production
with a proper web server such as **NGINX**, **Caddy**, or **Apache**.

This repo attempts to blend the two by still using **FPM** and **Web Server**, but
keeping configuration, logs, and other files local to your project.

This is heavily inspired by the Symfony [web-server-bundle](https://github.com/symfony/web-server-bundle).
