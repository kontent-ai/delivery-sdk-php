<?php

namespace Kontent\Ai\Tests\E2E\Utils;

use Kontent\Ai\Delivery\ContentLinkUrlResolverInterface;

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