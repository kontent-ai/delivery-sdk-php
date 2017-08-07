<?php

namespace KenticoCloud\Delivery;

class UrlBuilder
{
    public $projectID = null;
    public $usePreviewApi = false;
    public $previewEndpoint  = 'https://preview-deliver.kenticocloud.com/';
    public $productionEndpoint  = 'https://deliver.kenticocloud.com/';

    public function __construct($projectID, $usePreviewApi = null)
    {
        $this->projectID = $projectID;
        $this->usePreviewApi = $usePreviewApi;
    }
    
    public function buildURL($endpoint, $query = null)
    {
        $segments = array(
            trim($this->usePreviewApi ? $this->previewEndpoint : $this->productionEndpoint, '/'),
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
