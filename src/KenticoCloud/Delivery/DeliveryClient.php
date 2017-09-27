<?php

namespace KenticoCloud\Delivery;

class DeliveryClient
{
    public $previewMode = false;
    public $urlBuilder = null;
    public $previewApiKey = null;
    public $_debug = true;
    public $lastRequest = null;
    public $mode = null;
    protected $typeMapper = null;
    protected $propertyMapper = null;
    protected $modelBinder = null;
    protected $contentTypeFactory = null;
    protected $taxonomyFactory = null;

    public function __construct($projectId, $previewApiKey = null, TypeMapperInterface $typeMapper = null, PropertyMapperInterface $propertyMapper = null)
    {
        $this->previewApiKey = $previewApiKey;
        $this->previewMode = !is_null($previewApiKey);
        $this->urlBuilder = new UrlBuilder($projectId, $this->previewMode);
        $this->typeMapper = $typeMapper;
        $this->propertyMapper = $propertyMapper;
        $self = get_class($this);
    }

    public function getItems($params)
    {
        $uri = $this->urlBuilder->getItemsUrl($params);
        $request = $this->getRequest($uri);
        $response = $this->send($request);

        $modelBinder = $this->getModelBinder();
                
        $items = new Models\ContentItems($modelBinder, $response->body);

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

    /**
     * Retrieves Content Types.
     *
     * @param $params QueryParams Specification of parameters for Content Types retrieval.
     * @return mixed array of corresponding content type objects
     */
    public function getTypes($params)
    {
        $uri = $this->urlBuilder->getTypesUrl($params);
        $request = $this->getRequest($uri);
        $response = $this->send($request);

        $typeFactory = $this->getContentTypeFactory();
        $types = $typeFactory->createTypes($response->body);

        return $types;
    }

    /**
     * Retrieves single content type.
     * TODO: Allow specifying content type by codename
     */
    public function getType($params)
    {
        $params['limit'] = 1;
        $results = $this->getTypes($params);
        
        if (count($results) != 1){
            return null;
        }

        $type = $results[0];
        return $type;
    }

    
    /**
     * Retrieves Taxonomies.
     *
     * @param $params QueryParams Specification of parameters for Taxonomy retrieval.
     * @return array of retrieved Taxonomies.
     */
    public function getTaxonomies($params)
    {
        $uri = $this->urlBuilder->getTaxonomiesUrl($params);
        $request = $this->getRequest($uri);
        $response = $this->send($request);

        $taxonomyFactory = $this->getTaxonomyFactory();
        $taxonomies = $taxonomyFactory->createTaxonomies($response->body);

        return $taxonomies;
    }


    /**
     * Retrieves single taxonomy.
     * TODO: Allow specifying taxonomy by codename
     */
    public function getTaxonomy($params)
    {
        $params['limit'] = 1;
        $results = $this->getTaxonomies($params);

        if (count($results) != 1)
        {
            return null;
        }

        return ($results[0]);
    }

    protected function getRequest($uri)
    {
        //TODO: make use of templates http://phphttpclient.com/#templates
        $request = \Httpful\Request::get($uri);
       # $request->_debug = $this->_debug;
        $request->mime('json');
        if (!is_null($this->previewApiKey)) {
            $request->addHeader('Authorization', 'Bearer ' . $this->previewApiKey);
        }
        return $request;
    }

    protected function send($request)
    {
        $response = $request->send();
        $this->lastRequest = $request;
        $this->lastResponse = $response;
        return $response;
    }

    protected function getModelBinder()
    {
        if ($this->modelBinder == null) {
            if ($this->typeMapper == null || $this->propertyMapper == null) {
                $defaultMapper = new DefaultMapper();
            }
            $this->modelBinder = new ModelBinder($this->typeMapper ?? $defaultMapper, $this->propertyMapper ?? $defaultMapper);
        }
        return $this->modelBinder;
    }

    protected function getContentTypeFactory()
    {
        if ($this->contentTypeFactory == null)
        {
            $this->contentTypeFactory = new ContentTypeFactory();
        }
        return $this->contentTypeFactory;
    }

    protected function getTaxonomyFactory()
    {
        if ($this->taxonomyFactory == null)
        {
            $this->taxonomyFactory = new TaxonomyFactory();
        }
        return $this->taxonomyFactory;
    }
}
