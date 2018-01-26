<?php
/**
 * Interface InlineModularContentResolverInterface resolve inline modular content.
 */

namespace KenticoCloud\Delivery;

/**
 * Interface InlineModularContentResolverInterface resolve inline modular content.
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
