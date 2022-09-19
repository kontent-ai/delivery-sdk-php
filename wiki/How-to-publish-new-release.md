# How to publish new release

1. **Check that in master branch in [Delivery client](../src/Kontent/Ai/Delivery/DeliveryClient.php) the private field called $sdkVersion match version you are going to release. If not edit this file to have the proper version set. This property is used for adding information to http request from what version of SDK are the requests send from.**

   ```php
   private $sdkVersion = '6.0.0';
   ```

1. Go to [SDK GitHub page](https://github.com/kontent-ai/delivery-sdk-php)
1. Select Releases
1. Click draft a new release
1. Define a tag value to the one that is defined in $sdkVersion field in [Delivery client](../src/Kontent/Ai/Delivery/DeliveryClient.php).
1. Define release title
1. To the description summarize new feature and important changes since the last release
1. (Optionally) If the version is pre-release, check This is a pre-release version
1. Hit publish release

## How does it work

- Administrator account [kontent-ai-bot](https://github.com/kontent-ai-bot) is connected to "kontent-ai" account on [Packagist](https://packagist.org).
  - This allows Packagist to register the webhook for new tag creation using [Packagist application](https://github.com/settings/connections/applications/a059f127e1c09c04aa5a)
  - For every created tag there is a new release made in [Packagist](https://packagist.org/packages/kontent-ai/delivery-sdk-php)
