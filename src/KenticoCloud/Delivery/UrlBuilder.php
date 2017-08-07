<?php

namespace KenticoCloud\Delivery;

class UrlBuilder
{
    public $projectID = null;
    public $usePreviewApi = false;
    public const PREVIEW_ENDPOINT  = 'https://preview-deliver.kenticocloud.com/';
    public const PRODUCTION_ENDPOINT  = 'https://deliver.kenticocloud.com/';
    
    private const URL_TEMPLATE_ITEM = '/items/{0}';
    private const URL_TEMPLATE_ITEMS = '/items';
    private const URL_TEMPLATE_TYPE = '/types/{0}';
    private const URL_TEMPLATE_TYPES = '/types';
    private const URL_TEMPLATE_ELEMENT = '/types/{0}/elements/{1}';

    public function __construct($projectID, $usePreviewApi = null)
    {
        $this->projectID = $projectID;
        $this->usePreviewApi = $usePreviewApi;
    }

    public function getItemsUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_ITEMS, $query);
    }
    
    private function buildUrl($endpoint, $query = null)
    {
        $segments = array(
            trim($this->usePreviewApi ? self::PREVIEW_ENDPOINT : self::PRODUCTION_ENDPOINT, '/'),
            trim($this->projectID, '/'),
            trim($endpoint, '/')
        );
        $url = implode('/', $segments);
        if (is_array($query)) {
            $query = http_build_query($query);
        }
        if (is_string($query)) {
            $url = rtrim($url, '?') . '?' . ltrim($query, '?');
        }

        return $url;
    }
}
