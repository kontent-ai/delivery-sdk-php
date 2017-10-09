# Short guide how to generate documentation


##Prerequisities

###phpDocumentator
Install phpDocumentator with PEAR.

````
pear channel-discover pear.phpdoc.org
pear install phpdoc/phpDocumentor
````

Source: https://phpdoc.org/

###Generate documentation from a command line
````
/usr/bin/php /usr/bin/phpdoc -d $ProjectFileDir$/src/ -t $ProjectFileDir$/phpdocs/docs
``


