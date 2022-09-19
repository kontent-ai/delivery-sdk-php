<?php
/**
 * Interface ContentLinkUrlResolverInterface resolve links URLs.
 */

namespace Kontent\Ai\Delivery;

use Kontent\Ai\Delivery\Models\Items\ContentLink;

/**
* Interface ContentLinkUrlResolverInterface resolve links URLs.
 */
interface ContentLinkUrlResolverInterface
{
    /**
     * Returns a URL of the linked content item. Default implementation returns empty string.
     *
     * @param Kontent\Ai\Delivery\Models\Items\ContentLink $link The link to a content item that needs to be resolved.
     *
     * @return string
     */
    public function resolveLinkUrl($link);


    /**
     * Returns a URL of the linked content item that is not available.
     *
     * @return string
     */
    public function resolveBrokenLinkUrl();
}
