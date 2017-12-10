# dependencies
This is a package for framework laravel 5.5+

# about
This is some simple backend code to upload a file and also to stream a file as base64_encoded.

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