<?php

namespace KenticoCloud\Delivery;

class Client
{
    const MODE_PREVIEW = 1;
    const MODE_PUBLISHED = 2;

    public $projectID = null;
    public $apiKey = null;
    public $uri = 'https://deliver.kenticocloud.com/';
    public $_debug = true;
    public $lastRequest = null;
    public $mode = null;

    public function __construct($projectID, $apiKey = null)
    {
        $this->projectID = $projectID;
        $this->apiKey = $apiKey;
        $self = get_class($this);
        $this->mode = $self::MODE_PUBLISHED;
    }
    
    public function buildURL($endpoint, $query = null)
    {
        $segments = array(
            trim($this->uri, '/'),
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

    public function setRequestAuthorization($request)
    {
        $request->addHeader('Authorization', 'Bearer ' . $this->apiKey);
        return $request;
    }

    public function prepRequest($request)
    {
        $request->_debug = $this->_debug;
        $request->mime('json');
        $request = $this->setRequestAuthorization($request);
        $this->lastRequest = $request;
        return $request;
    }

    public function getRequest($endpoint, $params = null)
    {
        $uri = $this->buildURL($endpoint, $params);
        
        $request = \Httpful\Request::get($uri);
        $request = $this->prepRequest($request);
        return $request;
    }

    public function send($request = null)
    {
        if (!$request) {
            $request = $this->lastRequest;
        } else {
            $this->lastRequest = $request;
        }
        $response = $request->send();
        $this->lastResponse = $response;
        return $response;
    }

    public function getItems($params)
    {
        $request = $this->getRequest('items', $params);
        $response = $this->send();
        
        $items = Models\ContentItems::create($response->body);

        return $items;
    }

    public function getItem($params)
    {
        $params['limit'] = 1;
        $results = $this->getItems($params);

        if (!isset($results->items) || !count($results->items)) {
            return null;
        }

        $item = reset($results->items);
        return $item;
    }
}
