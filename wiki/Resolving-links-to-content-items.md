# Resolving links to content items

## Contents

<!-- TOC -->

- [Resolving links to content items](#resolving-links-to-content-items)
  - [Contents](#contents)
  - [Content links](#content-links)
  - [Implementing a resolver](#implementing-a-resolver)
  - [Registering a resolver](#registering-a-resolver)
  - [Retrieving Rich text content](#retrieving-rich-text-content)

<!-- /TOC -->

## Content links

[Rich text elements](https://developer.kenticocloud.com/reference#content-type-element-object) in Kontent.ai can contain links to other content items. For example, if you run a blog, these content item links might represent hyperlinks to other blog posts or your contact page.

Without adjusting your project, any link in a Rich text element that points to a content item will contain an empty value.

```html
<p>
  Each AeroPress comes with a
  <a href="" data-item-id="65832c4e-8e9c-445f-a001-b9528d13dac8"
    >pack of filters</a
  >
  included in the box.
</p>
```

To make sure such links resolve correctly on your website, you need to complete these steps:

1. Implement a content link URL resolver
1. Register the resolver within the `DeliveryClient` instance
1. Retrieve content of a Rich text element

## Implementing a resolver

Your resolver must implement the `ContentLinkUrlResolverInterface` interface, which defines two methods for resolving URLs to content items, `ResolveLinkUrl` and `ResolveBrokenLinkUrl`.

- **ResolveLinkUrl** – used when the linked content item is available.
- **ResolveBrokenLinkUrl** – used when the linked content item is not available.

When are content items available?

- For live environment, a content item is available when published, and unavailable when deleted or unpublished.
- For preview environment, a content item is available when it exists in the project inventory, and unavailable when deleted.

```php
// Sample resolver implementation
class CustomContentLinkUrlResolver implements ContentLinkUrlResolverInterface
{
    public function resolveLinkUrl($link)
    {
        if ($link->contentTypeCodeName == "accessory") {
            return "/accessories/". $link->urlSlug;
        }

        // TODO: Add the rest of the resolver logic
    }

    public function resolveBrokenLinkUrl()
    {
        // Resolves URLs to unavailable content items
        return "/404";
    }
}
```

When building the resolver logic, you can use the `link` parameter in your code.

The `link` parameter provides the following information about the linked content item:

| Property              | Description                                                                                                                                | Example                                |
| --------------------- | ------------------------------------------------------------------------------------------------------------------------------------------ | -------------------------------------- |
| `id`                  | The identifier of the linked content item.                                                                                                 | `65832c4e-8e9c-445f-a001-b9528d13dac8` |
| `codename`            | The codename of the linked content item.                                                                                                   | `aeropress_filters`                    |
| `urlSlug`             | The URL slug of the linked content item. The value is `null` if the item's content type doesn't have a URL slug element in its definition. | `aeropress-filters`                    |
| `contentTypeCodename` | The codename of the content type of the linked content item.                                                                               | `accessory`                            |

## Registering a resolver

Once you implement the resolver, you need to register it in the `DeliveryClient`.

```php
// Sets the resolver as an optional dependency of the DeliveryClient
$client = new DeliveryClient("975bf280-fd91-488c-994c-2f04416e5ee3");
$client->contentLinkUrlResolver = new CustomContentLinkUrlResolver();
```

## Retrieving Rich text content

Now, you can resolve links in Rich text elements by using the `GetString` method on the `ContentItem` object.

```php
// Retrieves the 'aeropress' content item
$item = $client->getItem('aeropress');

// Retrieves text from the 'long_description' Rich text element
$description = $item->longDescription;
```

The URL to the content item in the text is now correctly resolved.

```html
<p>
  Each AeroPress comes with a
  <a
    href="/accessories/aeropress-filters"
    data-item-id="65832c4e-8e9c-445f-a001-b9528d13dac8"
    >pack of filters</a
  >
  included in the box.
</p>
```

![Analytics](https://kentico-ga-beacon.azurewebsites.net/api/UA-69014260-4/Kentico/kontent-delivery-sdk-php/wiki/Resolving-links-to-content-items?pixel)
