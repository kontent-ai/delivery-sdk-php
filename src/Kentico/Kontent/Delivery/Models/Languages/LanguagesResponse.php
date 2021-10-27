<?php
/**
 * Represents a project language.
 */

namespace Kentico\Kontent\Delivery\Models\Languages;

/**
 * Class LanguagesResponse
 * @package Kentico\Kontent\Delivery\Models\Languages
 */
class LanguagesResponse
{
    /**
     * Returns an array of content languages.
     * @var Language[]
     */
    public $languages = null;
    
    /**
     * Gets data about the page size, current page, etc.
     * @var \Kentico\Kontent\Delivery\Models\Shared\Pagination::class
     */
    public $pagination = null;

    /**
     * LanguagesResponse constructor.
     * @param $languages Array of languages.
     * @param $pagination Pagination information (size, offset, etc.)
     */
    public function __construct($languages, $pagination)
    {
        $this->languages = $languages;
        $this->pagination = $pagination;
        return $this;
    }
}
