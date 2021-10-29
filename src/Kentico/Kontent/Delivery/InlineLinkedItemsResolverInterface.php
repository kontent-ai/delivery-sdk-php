<?php
/**
 * Interface InlineLinkedItemsResolverInterface resolve inline linked items.
 */

namespace Kentico\Kontent\Delivery;

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
     * @param mixed|null $linkedItems JSON response containing nested linked items
     * 
     * @return string
     */
    public function resolveInlineLinkedItems($input, $item, $linkedItems);
}
