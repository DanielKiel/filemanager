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

# using filemanager

## Listing files

```
    GET /services/filemanager/v1/
```

Possible options you can set as query param:

``` 

    orderBy : created_at DESC // specify the ordering of your list
    published: 1 //get only published files
    dir: name-of-the-dir //get only files of specified directory
    per_page: 25 //how much elements a response should contain

```

## Uploading a file

```
    POST /services/filemanager/v1/{uploadParam}/{directory?}
```

At uploadParam you define the name of your input field which contains the uploaded file. Default will be "upload".
At directory you define the directory where to store the file.
To avoid breaking the API, this is now an optional param. You can also post following instead of using directory:

```
    POST /services/filemanager/v1/{uploadParam}
    {
        "directory": "path/to/your/file" //we can define a hierarchical structure, folders will be generated
    }
```

## Getting a file

1. Getting an image

```
    GET /services/filemanager/v1/{fileId}/{thumb?}
```

The default of thumb is 1 - you will get a thumb preview. Default of thumb width is 180px, but you can change this at the config (widen).
When set thumb to 0 you will get the original image.

2. Getting a preview of a file

```
    GET /services/filemanager/preview/v1/{fileId}/
```

Here you get a preview. At the moment it is a simple tet preview for example to show the content of a pdf or a csv.

## Updating a file

It is possible to update the object, for example to change published flag or the name of a file

!!! Be careful what you do !!! Ath the moment there is no automatism when you for example change the dir attribute of a file !!!

```
    PUT /services/filemanager/v1/{fileId}
```

## Deleting a file

Deleting a file object will also delete the file at the disk

```
    DELETE /services/filemanager/v1/{fileId}
```




Vue Component requires: 

- https://www.npmjs.com/package/filesize
- https://vuematerial.io

