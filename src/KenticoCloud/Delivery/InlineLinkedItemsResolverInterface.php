<?php
/**
 * Interface InlineLinkedItemsResolverInterface resolve inline linked items.
 */

namespace KenticoCloud\Delivery;

/**
 * Interface InlineLinkedItemsResolverInterface resolve inline linked items.
 */
interface InlineLinkedItemsResolverInterface
{
    /**
     * Return resolved inline linked items.
     * 
     * @param string $input input html of inline linked items.
     * @param mixed $item data for inline linked items.
     * 
     * @return string
     */
    public function resolveInlineLinkedItems($input, $item);
}
