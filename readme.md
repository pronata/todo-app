# Framework Name • [TodoMVC](http://todomvc.com)

> Official description of the framework (from its website)


## Resources

- [Website]()
- [Documentation]()
- [Used by]()
- [Blog]()
- [FAQ]()

### Articles

- [Interesting article]()

### Support

- [Stack Overflow](http://stackoverflow.com/questions/tagged/__)
- [Google Groups]()
- [Twitter](http://twitter.com/__)
- [Google+]()

*Let us [know](https://github.com/tastejs/todomvc/issues) if you discover anything worth sharing.*


## Implementation

How was the app created? Anything worth sharing about the process of creating the app? Any spec violations?


## Credit

Created by [Your Name](http://your-website.com)


## nginx config for backend
```
server {
    server_name todo-api.loc www.todo-api.loc;
    root /path-to-webdata-folder/todo-app/backend/web;

    location / {
        # try to serve file directly, fallback to front.php
        try_files $uri /front.php$is_args$args;
    }
  
    location ~ ^/front\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        internal;
   }

   # return 404 for all other php files not matching the front controller
   # this prevents access to other php files you don't want to be accessible.
   location ~ \.php$ {
     return 404;
   }

   error_log /var/log/nginx/todo-api_error.log;
   access_log /var/log/nginx/todo-api_access.log;
}
```

```
<VirtualHost *:80>
    ServerName todo-api.loc
    ServerAlias www.todo-api.loc
    DocumentRoot "path-to-webdata-folder/todo-app/backend/web"
	<Directory path-to-webdata-folder/todo-app/backend/web>
        AllowOverride None
        Order Allow,Deny
        Allow from All

        <IfModule mod_rewrite.c>
            Options -MultiViews
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ front.php [QSA,L]
        </IfModule>
    </Directory>

    ErrorLog "path-to-log-folder/logs/todo-api-error.log"
    CustomLog "path-to-log-folder/logs/todo-api-access.log" common
</VirtualHost>
```
## commands to create database tables
```
Windows:
cd backend
sh vendor/bin/doctrine orm:schema-tool:create
sh vendor/bin/doctrine orm:schema-tool:drop --force
sh vendor/bin/doctrine orm:schema-tool:update --force
sh vendor/bin/doctrine orm:schema-tool:update --force --dump-sql
Doctrine uses Proxies to connect to the database. 
sh vendor/bin/doctrine orm:generate-proxies

```
## Api description and database scheme
```
https://repository.genmymodel.com/pronata/todo-list
https://docs.google.com/document/d/1cVA9uJvZF3PI8_xdE1zyH6ihseFTRN_Z_0zkBvwH9Z0/edit?usp=sharing
```
## TODO
- Authorization not only with anonymous user
- Sharing lists
- Caching
- Testing

