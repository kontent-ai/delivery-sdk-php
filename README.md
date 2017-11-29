# Kentico Cloud Delivery SDK for PHP
[![Build Status](https://travis-ci.org/Kentico/delivery-sdk-php.svg?branch=master)](https://travis-ci.org/Kentico/delivery-sdk-php)
[![Packagist](https://img.shields.io/packagist/v/kentico-cloud/delivery-sdk-php.svg)](https://packagist.org/packages/kentico-cloud/delivery-sdk-php)
[![Test Coverage](https://codeclimate.com/github/Kentico/delivery-sdk-php/badges/coverage.svg)](https://codeclimate.com/github/Kentico/delivery-sdk-php/coverage)
[![Code Climate](https://codeclimate.com/github/Kentico/delivery-sdk-php/badges/gpa.svg)](https://codeclimate.com/github/Kentico/delivery-sdk-php)
[![Forums](https://img.shields.io/badge/chat-on%20forums-orange.svg)](https://forums.kenticocloud.com)
[![Docs](https://img.shields.io/badge/documentation-API--Reference-green.svg)](https://kentico.github.io/phpsdk/index.html)

🚧 **This project is in BETA. Let us know if you want to help us test or contribute otherwise at DEVELOPERSCOMMUNITY@KENTICO.COM** 🚧 


## Summary

The Kentico Cloud Delivery PHP SDK is a client library used for retrieving content from Kentico Cloud. The best way to use the SDK is to consume it in the form of a [Packagist package](https://packagist.org/packages/kentico-cloud/delivery-sdk-php).

## Prerequisites

To retrieve content from a Kentico Cloud project via the Delivery API, you first need to activate the API for the project. See our documentation on how you can [activate the Delivery API](https://developer.kenticocloud.com/docs/using-delivery-api#section-enabling-the-delivery-api-for-your-projects).


## Using the DeliveryClient

The `DeliveryClient` class is the main class of the SDK. Using this class, you can retrieve content from your Kentico Cloud projects.

To create an instance of the class, you need to provide a [project ID](https://developer.kenticocloud.com/docs/using-delivery-api#section-getting-project-id).

```php
// Initializes an instance of the DeliveryClient client
$client = new DeliveryClient('975bf280-fd91-488c-994c-2f04416e5ee3');
```

There are some other optional parameters that you can use during the `DeliveryClient` instantiation.

* `$previewApiKey` – sets the Delivery Preview API key. The client will automatically start using the preview endpoint for querying. See [previewing unpublished content](#previewing-unpublished-content).
* `$waitForLoadingNewContent` – makes the client instance wait while fetching updated content, useful when acting upon [webhook calls](https://developer.kenticocloud.com/docs/webhooks#section-requesting-new-content).
* `$debugRequests` – switches the HTTP client to debug mode


Once you create a `DeliveryClient`, you can start querying your project repository by calling methods on the client instance. See [Basic querying](#basic-querying) for details.

## Basic querying

Once you have a `DeliveryClient` instance, you can start querying your project repository by calling methods on the instance.

```php
// Retrieves a single content item
$item = $client->getItem('about_us');

// Retrieves a list of all content items
$items = $client->getItems();
```


### Filtering retrieved data

The SDK supports full scale of the API querying and filtering capabilities as described in the [API reference](https://developer.kenticocloud.com/reference#filtering-content-items).

```php
// Retrieves a list of the specified elements from the first 10 content items of
// the 'brewer' content type, ordered by the 'product_name' element value
$response = $client->getItems((new QueryParams())
  ->equals('system.type', 'brewer')
  ->elements(array('image', 'price', 'product_status','processing'))
  ->limit(10)
  ->orderAsc('elements.product_name'));
```


## Feedback & Contributing

Check out the [contributing](https://github.com/Kentico/delivery-sdk-php/blob/master/CONTRIBUTING.md) page to see the best places to file issues, start discussions, and begin contributing.

### Developing on Windows
Have a look at our cool [tutorial](https://github.com/Kentico/delivery-sdk-php/wiki) on developing PHP on Windows with Visual Studio Code!

### Developing on Linux
Do you prefer penguins? Check out the [tutorial](https://github.com/Kentico/delivery-sdk-php/wiki/Configuring-PHP-Storm-on-Linux) on developing PHP on Linux with PhpStorm!

### Wall of Fame
We would like to express our thanks to the following people who contributed and made the project possible:

- [Stephen Rushing](https://github.com/stephenr85/) - [eSiteful](http://www.esiteful.com/home) - [ORIGINAL WORK](https://github.com/stephenr85/KenticoCloud.Deliver.PHP)

Would you like to become a hero too? Pick an [issue](https://github.com/Kentico/delivery-sdk-php/issues) and send us a pull request!
