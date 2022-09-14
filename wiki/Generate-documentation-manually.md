# Short guide how to generate documentation

## Prerequisites

### phpDocumentator

[Install phpDocumentator](https://docs.phpdoc.org/3.0/guide/getting-started/installing.html)

- Use PHAR download

Linux download:

```sh
wget https://phpdoc.org/phpDocumentor.phar
chmod +x phpDocumentor.phar
```

Source: [https://phpdoc.org/](https://phpdoc.org/)

### Generate documentation from a command line

```sh
php phpDocumentor.phar -d $ProjectFileDir$/src/ -t $ProjectFileDir$/phpdocs/docs
```

A complete example

Windows

```cmd
php C:\php\phpDocumentor.phar -d C:\projects\kontent-delivery-sdk-php\src\ -t C:\projects\kontent-delivery-sdk-php\phpdocs\docs
```
