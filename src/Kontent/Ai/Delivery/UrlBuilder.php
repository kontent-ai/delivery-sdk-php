<?php
/**
 * Helper class for building url.
 */

namespace Kontent\Ai\Delivery;

/**
 * Class UrlBuilder.
 */
class UrlBuilder
{
    /**
     * Gets or sets the Environment identifier.
     *
     * @var string
     */
    public $environmentID = null;
    /**
     * Gets or sets whether the Preview API should be used. If TRUE, <see cref="PreviewApiKey"/> needs to be set as well.
     *
     * @var bool
     */
    public $usePreviewApi = false;
    const PREVIEW_ENDPOINT = 'https://preview-deliver.kontent.ai/';
    const PRODUCTION_ENDPOINT = 'https://deliver.kontent.ai/';

    const URL_TEMPLATE_ITEM = '/items/%s';
    const URL_TEMPLATE_ITEMS = '/items';
    const URL_TEMPLATE_TYPE = '/types/%s';
    const URL_TEMPLATE_TYPES = '/types';
    const URL_TEMPLATE_ELEMENT = '/types/%s/elements/%s';
    const URL_TEMPLATE_TAXONOMIES = '/taxonomies';
    const URL_TEMPLATE_TAXONOMY = '/taxonomies/%s';
    const URL_TEMPLATE_LANGUAGES = '/languages';

    /**
     * UrlBuilder constructor.
     *
     * @param $environmentID
     * @param false $usePreviewApi
     */
    public function __construct(string $environmentID, bool $usePreviewApi = null)
    {
        $this->environmentID = $environmentID;
        $this->usePreviewApi = $usePreviewApi ?? $this->usePreviewApi;
    }

    /**
     * Get url for specifed item.
     *
     * @param $codename
     * @param $query
     *
     * @return string
     */
    public function getItemUrl($codename, $query = null)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_ITEM, urlencode($codename)), $query);
    }

    /**
     * Get items by query.
     *
     * @param null $query
     *
     * @return string
     */
    public function getItemsUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_ITEMS, $query);
    }

    /**
     * Returns URL to specified Content Type endpoint.
     *
     * @param $codename string Content Type code name
     *
     * @return string URL pointing to specific Content Type endpoint
     */
    public function getTypeUrl($codename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_TYPE, urlencode($codename)));
    }

    /**
     * Returns URL to all Content Types endpoint.
     *
     * @param $query queryParams Specification of parameters for Content Types request
     *
     * @return string URL pointing to Content Types endpoint
     */
    public function getTypesUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_TYPES, $query);
    }

    /**
     * Returns URL to Taxonomy endopoint.
     *
     * @param $codename string Codename of specific taxonomy to be retrieved
     *
     * @return string URL pointing to Taxonomy endpoint
     */
    public function getTaxonomyUrl($codename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_TAXONOMY, urlencode($codename)));
    }

    /**
     * Returns URL to all taxonomies endpoint.
     *
     * @param object queryParams Specification of parameters for Taxonomies request
     *
     * @return string URL pointing to all taxonomies endpoint
     */
    public function getTaxonomiesUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_TAXONOMIES, $query);
    }

    /**
     * Returns URL to all languages endpoint.
     *
     * @param object queryParams Specification of parameters for Languages request
     *
     * @return string URL pointing to all languages endpoint
     */
    public function getLanguagesUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_LANGUAGES, $query);
    }

    /**
     * Returns URL to content element endpoint.
     *
     * @param $contentTypeCodename string Codename for specified content type
     * @param $contentElementCodename string Codename for specified content element
     *
     * @return string URL to content element endpoint
     */
    public function getContentElementUrl($contentTypeCodename, $contentElementCodename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_ELEMENT, urlencode($contentTypeCodename), urlencode($contentElementCodename)));
    }

    /**
     * Build url for given endpoint and query.
     *
     * @param $endpoint
     * @param null $query
     *
     * @return string
     */
    private function buildUrl($endpoint, $query = null)
    {
        $segments = array(
            trim($this->usePreviewApi ? self::PREVIEW_ENDPOINT : self::PRODUCTION_ENDPOINT, '/'),
            trim($this->environmentID, '/'),
            trim($endpoint, '/'),
        );
        $url = implode('/', $segments);

        if (is_a($query, QueryParams::class)) {
            $query = $query->data;
        }
        if (is_array($query)) {
            $query = http_build_query($query);
        }
        if (is_string($query)) {
            $url = rtrim($url, '?').'?'.ltrim($query, '?');
        }

        return $url;
    }
}
