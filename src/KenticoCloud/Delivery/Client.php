<?php

namespace KenticoCloud\Delivery;

class Client
{
    const MODE_PREVIEW = 1;
    const MODE_PUBLISHED = 2;

    public $urlBuilder = null;
    public $previewApiKey = null;
    public $_debug = true;
    public $lastRequest = null;
    public $mode = null;

    public function __construct($projectId, $previewApiKey = null)
    {
        $this->previewApiKey = $previewApiKey;
        $this->urlBuilder = new UrlBuilder($projectId, !is_null($previewApiKey));
        $self = get_class($this);
        $this->mode = $self::MODE_PUBLISHED;
    }    

    public function setRequestAuthorization($request)
    {
        $request->addHeader('Authorization', 'Bearer ' . $this->previewApiKey);
        return $request;
    }

    public function getRequest($uri, $params = null)
    {        
        $request = \Httpful\Request::get($uri);
        $request->_debug = $this->_debug;
        $request->mime('json');
        $request = $this->setRequestAuthorization($request);
        $this->lastRequest = $request;        
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
        $uri = $this->urlBuilder->getItemsUrl($params);
        $request = $this->getRequest($uri, $params);
        $response = $this->send();
        
        $items = Models\ContentItems::create($response->body);

        return $items;
    }

    public function getItem($params)
    {
        //TODO: use the 'item' endpoint (https://deliver.kenticocloud.com/975bf280-fd91-488c-994c-2f04416e5ee3/items/home)
        $params['limit'] = 1;
        $results = $this->getItems($params);

        if (!isset($results->items) || !count($results->items)) {
            return null;
        }

        $item = reset($results->items);
        return $item;
    }
}
