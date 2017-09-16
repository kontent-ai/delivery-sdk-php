<?php

namespace KenticoCloud\Delivery;

class UrlBuilder
{
    public $projectID = null;
    public $usePreviewApi = false;
	const PREVIEW_ENDPOINT  = 'https://preview-deliver.kenticocloud.com/';
	const PRODUCTION_ENDPOINT  = 'https://deliver.kenticocloud.com/';
    
    const URL_TEMPLATE_ITEM = '/items/%s';
    const URL_TEMPLATE_ITEMS = '/items';
    const URL_TEMPLATE_TYPE = '/types/%s';
    const URL_TEMPLATE_TYPES = '/types';
    const URL_TEMPLATE_ELEMENT = '/types/%s/elements/%s';

    public function __construct($projectID, $usePreviewApi = null)
    {
        $this->projectID = $projectID;
        $this->usePreviewApi = $usePreviewApi;
    }

    public function getItemUrl($codename, $query)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_ITEM, urlencode($codename)), $query);
    }

    public function getItemsUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_ITEMS, $query);
    }

    public function getTypeUrl($codename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_TYPE, urlencode($codename)));
    }

    public function getTypesUrl($query = null)
    {
        return $this->buildUrl(self::URL_TEMPLATE_TYPES, $query);
    }

    public function getContentElementUrl($contentTypeCodename, $contentElementCodename)
    {
        return $this->buildUrl(sprintf(self::URL_TEMPLATE_ELEMENT, urlencode($contentTypeCodename), urlencode($contentElementCodename)));
    }
    
    private function buildUrl($endpoint, $query = null)
    {
        $segments = array(
            trim($this->usePreviewApi ? self::PREVIEW_ENDPOINT : self::PRODUCTION_ENDPOINT, '/'),
            trim($this->projectID, '/'),
            trim($endpoint, '/')
        );
        $url = implode('/', $segments);
        
        if (is_a($query, \KenticoCloud\Delivery\QueryParams::class)) {
            $query = $query->data;
        }
        if (is_array($query)) {
            $query = http_build_query($query);
        }
        if (is_string($query)) {
            $url = rtrim($url, '?') . '?' . ltrim($query, '?');
        }

        return $url;
    }
}
