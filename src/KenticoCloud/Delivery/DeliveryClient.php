<?php
/**
 * Executes requests against the Kentico Cloud Delivery API.
 */

namespace KenticoCloud\Delivery;

/**
 * Class DeliveryClient
 * @package KenticoCloud\Delivery
 */
class DeliveryClient
{
    public $previewMode = false;
    public $urlBuilder = null;
    public $previewApiKey = null;
    public $debugRequests = false;
    public $lastRequest = null;
    public $mode = null;
    public $waitForLoadingNewContent = false;
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
    }

    public function getItems($params)
    {
        $uri = $this->urlBuilder->getItemsUrl($params);
        $request = $this->getRequest($uri);
        $response = $this->send($request);

        $modelBinder = $this->getModelBinder();
        
        $properties = get_object_vars($response->body);
        
        // Items
        $items = $modelBinder->getContentItems($properties['items'], $properties['modular_content']);

        // Pagination
        $pagination = $modelBinder->bindModel(\KenticoCloud\Delivery\Models\Shared\Pagination::class, $properties['pagination']);
                
        $itemsResponse = new Models\Items\ContentItemsResponse($items, $pagination);

        return $itemsResponse;
    }

    public function getItem($codename, $params = null)
    {
        $uri = $this->urlBuilder->getItemUrl($codename, $params);

        $request = $this->getRequest($uri);
        $response = $this->send($request);

        $modelBinder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        if (!isset($properties['item']) || !count($properties['item'])) {
            return null;
        }

        // Bind content item
        $item = $modelBinder->getContentItem($properties['item'], $properties['modular_content']);

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
        
        $modelBinder = $this->getModelBinder();

        //TODO: pass "types" only
        $properties = get_object_vars($response->body);

        // Bind content types
        $types = $typeFactory->createTypes($properties['types']);
        
        // Pagination
        $pagination = $modelBinder->bindModel(\KenticoCloud\Delivery\Models\Shared\Pagination::class, $properties['pagination']);

        $typesResponse = new Models\Types\ContentTypesResponse($types, $pagination);

        return $typesResponse;
    }

    /**
     * Retrieves single content type.
     */
    public function getType($codename)
    {
        $uri = $this->urlBuilder->getTypeUrl($codename);

        $request = $this->getRequest($uri);
        $response = $this->send($request);

        $typeFactory = $this->getContentTypeFactory();

        $properties = get_object_vars($response->body);

        if (!isset($properties['system']) || !count($properties['system'])) {
            return null;
        }

        // Bind content type
        $type = $typeFactory->createType($response->body);

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
        
        $modelBinder = $this->getModelBinder();
        
        $properties = get_object_vars($response->body);
        
        // Taxonomies
        $taxonomies = $taxonomyFactory->createTaxonomies($response->body);
                
        // Pagination
        $pagination = $modelBinder->bindModel(\KenticoCloud\Delivery\Models\Shared\Pagination::class, $properties['pagination']);
        
        $taxonomiesResponse = new Models\Taxonomies\TaxonomiesResponse($taxonomies, $pagination);
        
        return $taxonomiesResponse;
    }


    /**
     * Retrieves single taxonomy by its codename.
     *
     * @param $codename string Codename of taxonomy object to be retrieved
     * @return Taxonomy object Retrieved taxonomy, or null when taxonomy
     * with given codename does not exist.
     */
    public function getTaxonomy($codename)
    {
        $taxonomyUri = $this->urlBuilder->getTaxonomyUrl($codename);

        $request = $this->getRequest($taxonomyUri);
        $response = $this->send($request);

        // Syntax error, unexpected T_OBJECT_OPERATOR
        $taxonomy = ($this->getTaxonomyFactory())->createTaxonomy($response->body);

        return $taxonomy;
    }

    protected function getRequest($uri)
    {
        //TODO: make use of templates http://phphttpclient.com/#templates
        $request = \Httpful\Request::get($uri);
        $request->_debug = $this->debugRequests;
        $request->mime('json');
        if (!is_null($this->previewApiKey)) {
            $request->addHeader('Authorization', 'Bearer ' . $this->previewApiKey);
        }
        if ($this->waitForLoadingNewContent) {
            $request->addHeader('X-KC-Wait-For-Loading-New-Content', 'true');
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
        if ($this->contentTypeFactory == null) {
            $this->contentTypeFactory = new ContentTypeFactory();
        }
        return $this->contentTypeFactory;
    }

    protected function getTaxonomyFactory()
    {
        if ($this->taxonomyFactory == null) {
            $this->taxonomyFactory = new TaxonomyFactory();
        }
        return $this->taxonomyFactory;
    }
}
