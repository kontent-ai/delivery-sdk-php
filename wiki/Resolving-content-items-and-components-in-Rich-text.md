# Resolving content items and components

## Contents

<!-- TOC -->

- [Resolving content items and components](#resolving-content-items-and-components)
  - [Contents](#contents)
  - [Introduction](#introduction)
  - [Items and components in Rich text](#items-and-components-in-rich-text)
  - [Implementing a resolver](#implementing-a-resolver)
  - [Registering a resolver](#registering-a-resolver)
    - [Custom default resolver](#custom-default-resolver)
  - [Retrieving Rich text content](#retrieving-rich-text-content)

<!-- /TOC -->

## Introduction

This page describes how to resolve content items and components inside Rich text elements.

## Items and components in Rich text

[Rich text elements](https://developer.kenticocloud.com/v1/reference#section-rich-text-element) in Kontent.ai can contain other content items and components. For example, if you write a blog post, you might want to insert a video or testimonial to a specific place in your article.

_Note_: Items and components are resolved using the same mechanism; your application does not need to differentiate them. You can learn more about the differences between items and components in our [API Reference](https://developer.kenticocloud.com/v1/reference#linked-content).

Without adjusting your application, any content item or component in a Rich text element will resolve to an empty object reference, which won't be rendered on the page.

```html
<object
  type="application/kenticocloud"
  data-type="item"
  data-codename="donate_with_us"
></object>
```

To display the content in the rich text on your website, you need to define exactly how it should be rendered:

1. [Implement](#implementing-a-resolver) a content resolver
1. [Register](#registering-a-resolver) the resolver within the `DeliveryClient` instance
1. [Retrieve](#retrieving-rich-text-content) content of a Rich text element

For example, let's say you want to add YouTube videos to your article. In such a case, you would need a content type _YouTube video_ with a single Text element for the _Video ID_.

## Implementing a resolver

Your resolver must implement the `InlineLinkedItemsResolverInterface` interface, which defines the `resolveInlineLinkedItems()` method for resolving items and components to HTML markup.

**Note**: We recommend to return a valid HTML5 fragment.

```php
class CustomLinkedItemsResolver implements InlineLinkedItemsResolverInterface
{
    public function resolveInlineLinkedItems($input, $item, $linkedItems)
    {
        if(empty($item)){
            return $input;
        }

        switch ($item->system->type) {
            case 'youtube_video':
                return "<div><iframe type=\"text/html\" width=\"640\" height=\"385\" style=\"display:block; margin: auto; margin-top:30px ; margin-bottom: 30px\" src=\"https://www.youtube.com/embed/".$item->elements->video_id->value."?autoplay=1\" frameborder=\"0\"></iframe></div>";
        }

        return $input;
    }
}
```

If the resolver or the content item itself is not available, the object reference remains in html.

When are content items available?

- For the live environment, a content item is available when published, and unavailable when deleted or unpublished.
- For preview environment, a content item is available when it exists in the project inventory, and unavailable when deleted.

Components are an integral part of their content items and are always present in the response.

## Registering a resolver

Once you implement the resolver, you need to register it in the `DeliveryClient`.

```php
// Sets the resolver as an optional dependency of the DeliveryClient
$client = new DeliveryClient("975bf280-fd91-488c-994c-2f04416e5ee3");
$client->inlineLinkedItemsResolver= new CustomLinkedItemsResolver();
```

### Custom default resolver

If you need to customize the application behavior for cases when no resolution for type exists, you need to extend the `resolveInlineLinkedItems` method by the default statement.

```php
// Sample resolver implementation
class CustomLinkedItemsResolver implements InlineLinkedItemsResolverInterface
{
    public function resolveInlineLinkedItems($input, $item)
    {
        if(empty($item)){
            return $input;
        }

        switch ($item->system->type) {
            case 'youtube_video':
                return "<div><iframe type=\"text/html\" width=\"640\" height=\"385\" style=\"display:block; margin: auto; margin-top:30px ; margin-bottom: 30px\" src=\"https://www.youtube.com/embed/".$item->elements->video_id->value."?autoplay=1\" frameborder=\"0\"></iframe></div>";
            default:
                return "<div>Content not available.</div>";
        }
    }
}
```

## Retrieving Rich text content

Now, when you retrieve the content of a Rich text element via a property, items and components based on the _Youtube video_ will be resolved correctly.

```php
// Retrieves the 'Coffee beverages explained' article
 $item = $client->getItem('coffee_beverages_explained');

// Retrieves text from the 'body_copy' Rich text element
$description = $item->bodyCopy;
```

The HTML output of your content item resolver is now included in the Rich text.

```html
<div><iframe type=\"text/html\" width=\"640\" height=\"385\" style=\"display:block; margin: auto; margin-top:30px ; margin-bottom: 30px\" src=\"https://www.youtube.com/embed/wZZ7oFKsKzY?autoplay=1\" frameborder=\"0\"></iframe></div>
```
