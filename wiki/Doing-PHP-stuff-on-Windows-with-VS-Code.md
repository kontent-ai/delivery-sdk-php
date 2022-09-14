## Learn the syntax in 10 minutes

* [https://learnxinyminutes.com/docs/php](https://learnxinyminutes.com/docs/php)

## Package Management

* The main package management tooling for PHP is called Composer - [https://getcomposer.org](https://getcomposer.org)
  * It's necessary to have it [installed](https://github.com/Kentico/kentico-delivery-sdk-php/wiki/Developing-PHP-in-Visual-Studio-Code-for-Dummies) to successfully install/update/restore packages
* The main package source for PHP is called Packagist - [https://packagist.org/](https://packagist.org/)
* When you want to install project's packages (dependencies), simply run `composer install`

## Autoloading

Autoloading is a mechanism that saves you from calling `require` and `include` all the time to load all the necessary types. While there are bazillion ways to implement autoloading, there are some standard approaches, namely PSR-0 and [PSR-4](http://www.php-fig.org/psr/psr-4/). Luckily, the standard package management ecosystem - Composer implements both of them.
All you need to do is to specify what needs to be loaded in the [composer.json](https://github.com/Kentico/kontent-delivery-sdk-php/blob/master/composer.json) file. For instance:

```json
"autoload": {
        "psr-4": {
            "Kentico\\Kontent\\": "src/Kentico/Kontent"
        }
    }
```

Composer will automatically generate the code for you in the `\vendor\autoload.php` file when you call `composer install`. The code can be later used by another application using your library or by your own unit test bootstrapper (see below).

Sometimes, it's necessary to regenerate the autoloader. You can do that by calling [`composer dump-autoload`](https://getcomposer.org/doc/03-cli.md#dump-autoload). (This saves you from calling `composer install` or `composer update` all the time.)

More on autoloading at: [http://vegibit.com/composer-autoloading-tutorial/](http://vegibit.com/composer-autoloading-tutorial/)

## Unit Testing

### Test discovery

For a test to be discovered:

* The .php file needs to end with `Test` (case-sensitive). For instance: `ClientTest.php`
* The test methods need to start with `test` (case-sensitive). For instance `public function testMyCode()`
* PHPUnit needs to be instructed where to look for the tests. There are more ways of doing this, one of them is to provide the [`phpunit.xml` file](https://github.com/Kentico/kontent-delivery-sdk-php/blob/master/phpunit.xml)

    ```xml
    <?xml version="1.0" encoding="UTF-8"?>
    <phpunit bootstrap="tests/bootstrap.php">
        <testsuites>
            <testsuite name="unit">
                <directory>tests/Unit</directory>
            </testsuite>
            <testsuite name="integration">
                <directory>tests/Integration</directory>
            </testsuite>
            <testsuite name="e2e">
                <directory>tests/E2E</directory>
            </testsuite>
        </testsuites>
        <filter>
            <whitelist addUncoveredFilesFromWhitelist="true">
                <directory suffix=".php">src</directory>
            </whitelist>
        </filter>
    </phpunit>
    ```

  * notice the `bootstrap` attribute. It's an "autoloader" for unit tests. For the most part, it'll just call the standard [autoloader](https://github.com/Kentico/kontent-delivery-sdk-php/blob/master/tests/bootstrap.php) but you can specify some extra loading logic.
* to run unit tests simply run `phpunit`

## Debugging

## Running

* [https://technet.microsoft.com/en-us/library/hh994592(v=ws.11).aspx](https://technet.microsoft.com/en-us/library/hh994592(v=ws.11).aspx)
