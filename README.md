# dependencies
This is a package for framework laravel 5.5+

# about
This is some simple backend code to upload and read files.
Also some vue components are integrated - these ones are under development.

# install

```
    composer require dionyseos/filemanager
```

ServiceProvider auto detection was written, so no more stuff is necessary.

# config

```
    php artisan vendor:publish
```

When publishing config, you can set the middleware of auth which are protecting filemanager routes at
filemanager.middleware.auth. Default is auth, when using passport you can write auth:api here to switch middleware.

Also you can setup the value of "widen" to resize the width of the thumbs. Thumbs are generated on the fly when requesting a
thumbnail - the package do not store thumbs. But make sure you will flush the cache - the base64 encoding is cached with rememberForever.

Vue Component requires: 

- https://www.npmjs.com/package/filesize
- https://vuematerial.io


ChangeList:
v3 can now make a nested directory where file is saved. Therefor a route have been changed - 
you can now let directory being empty as query param but send directory as post request param.