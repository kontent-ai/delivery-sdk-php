<?php

namespace Kentico\Kontent\Tests\E2E\Utils;

use Kentico\Kontent\Delivery\ContentLinkUrlResolverInterface;

class CustomContentLinkUrlResolver implements ContentLinkUrlResolverInterface
{
    public function resolveLinkUrl($link)
    {
        return "/custom/$link->urlSlug";
    }

    public function resolveBrokenLinkUrl()
    {
        return "/404";
    }
}