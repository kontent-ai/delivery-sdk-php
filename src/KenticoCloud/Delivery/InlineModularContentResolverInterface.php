<?php
/**
 * Interface InlineModularContentResolverInterface resolve links URLs.
 */

namespace KenticoCloud\Delivery;

use KenticoCloud\Delivery\Models\Items\ContentLink;

/**
 * Interface InlineModularContentResolverInterface resolve links URLs.
 */
interface InlineModularContentResolverInterface
{
    /**
     * Return resolved inline modular content item.
     * 
     * @param string $input input html of inline modular content.
     * @param mixed $item data for inline modular content.
     * 
     * @return string
     */
    public function resolveInlineModularContent($input, $item);
}
