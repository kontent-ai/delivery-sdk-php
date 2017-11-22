<?php
/**
 * Represents information about a page.
 */

namespace KenticoCloud\Delivery\Models\Shared;

/**
 * Class Pagination.
 */
class Pagination
{
    const PAGINATION_ELEMENT_NAME = 'pagination';

    /**
     * @var int
     *          Gets the requested number of items to skip
     */
    public $skip = null;
    /**
     * @var int
     *          Gets the requested page size
     */
    public $limit = null;
    /**
     * @var int
     *          Gets the number of retrieved items
     */
    public $count = null;
    /**
     * @var string
     *             Gets the URL of the next page
     */
    public $nextPage = null;
}
