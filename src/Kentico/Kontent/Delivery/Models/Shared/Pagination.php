<?php
/**
 * Represents information about a page.
 */

namespace Kentico\Kontent\Delivery\Models\Shared;

/**
 * Class Pagination.
 */
class Pagination
{
    const PAGINATION_ELEMENT_NAME = 'pagination';

    /**
     * Gets the requested number of items to skip.
     *
     * @var int
     */
    public $skip = null;
    /**
     * Gets the requested page size.
     *
     * @var int
     */
    public $limit = null;

    /**
     * Gets the number of retrieved items.
     *
     * @var int
     */
    public $count = null;

    /**
     * Gets the URL of the next page.
     *
     * @var string
     */
    public $nextPage = null;

    /**
     * Gets the total number of items.
     *
     * @var string
     */
    public $totalCount = null;
}
