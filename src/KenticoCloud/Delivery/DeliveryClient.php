<?php
/**
 * Executes requests against the Kentico Cloud Delivery API.
 */

namespace KenticoCloud\Delivery;

use Httpful\Request;
use Httpful\Http;
use KenticoCloud\Delivery\Models\Shared\Pagination;

/**
 * Class DeliveryClient.
 */
class DeliveryClient
{
    private $urlBuilder = null;
    private $previewMode = false;
    protected $previewApiKey = null;
    protected $debugRequests = false;
    protected $waitForLoadingNewContent = false;
    public $typeMapper = null;
    public $propertyMapper = null;
    public $modelBinder = null;
    protected $contentTypeFactory = null;
    protected $taxonomyFactory = null;

    public function __construct($projectId, $previewApiKey = null, $waitForLoadingNewContent = false, $debugRequests = false)
    {
        $this->previewApiKey = $previewApiKey;
        $this->previewMode = !is_null($previewApiKey);
        $this->urlBuilder = new UrlBuilder($projectId, $this->previewMode);
        $this->waitForLoadingNewContent = $waitForLoadingNewContent;
        $this->debugRequests = $debugRequests;
        $this->initRequestTemplate();
    }

    public function getItems($params)
    {
        $uri = $this->urlBuilder->getItemsUrl($params);
        $response = $this->sendRequest($uri);

        $binder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        // Items
        $items = $binder->getContentItems($properties['items'], $properties['modular_content']);

        // Pagination
        $pagination = $binder->bindModel(Pagination::class, $properties[Pagination::PAGINATION_ELEMENT_NAME]);

        $itemsResponse = new Models\Items\ContentItemsResponse($items, $pagination);

        return $itemsResponse;
    }

    public function getItem($codename, $params = null)
    {
        $uri = $this->urlBuilder->getItemUrl($codename, $params);
        $response = $this->sendRequest($uri);

        $binder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        if (!isset($properties['item']) || !count($properties['item'])) {
            return null;
        }

        // Bind content item
        $item = $binder->getContentItem($properties['item'], $properties['modular_content']);

        return $item;
    }

    /**
     * Retrieves Content Types.
     *
     * @param $params queryParams Specification of parameters for Content Types retrieval
     *
     * @return mixed array of corresponding content type objects
     */
    public function getTypes($params)
    {
        $uri = $this->urlBuilder->getTypesUrl($params);
        $response = $this->sendRequest($uri);

        $typeFactory = $this->getContentTypeFactory();

        $binder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        // Bind content types
        $types = $typeFactory->createTypes($properties['types']);

        // Pagination
        $pagination = $binder->bindModel(Pagination::class, $properties[Pagination::PAGINATION_ELEMENT_NAME]);

        $typesResponse = new Models\Types\ContentTypesResponse($types, $pagination);

        return $typesResponse;
    }

    /**
     * Retrieves single content type.
     */
    public function getType($codename)
    {
        $uri = $this->urlBuilder->getTypeUrl($codename);
        $response = $this->sendRequest($uri);

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
     * @param $params queryParams Specification of parameters for Taxonomy retrieval
     *
     * @return array of retrieved Taxonomies
     */
    public function getTaxonomies($params)
    {
        $uri = $this->urlBuilder->getTaxonomiesUrl($params);
        $response = $this->sendRequest($uri);

        $factory = $this->getTaxonomyFactory();

        $binder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        // Taxonomies
        $taxonomies = $factory->createTaxonomies($response->body);

        // Pagination
        $pagination = $binder->bindModel(Pagination::class, $properties[Pagination::PAGINATION_ELEMENT_NAME]);

        $taxonomiesResponse = new Models\Taxonomies\TaxonomiesResponse($taxonomies, $pagination);

        return $taxonomiesResponse;
    }

    /**
     * Retrieves single taxonomy by its codename.
     *
     * @param $codename string Codename of taxonomy object to be retrieved
     *
     * @return Taxonomy object Retrieved taxonomy, or null when taxonomy
     *                  with given codename does not exist
     */
    public function getTaxonomy($codename)
    {
        $uri = $this->urlBuilder->getTaxonomyUrl($codename);
        $response = $this->sendRequest($uri);

        // Syntax error, unexpected T_OBJECT_OPERATOR
        $taxonomy = ($this->getTaxonomyFactory())->createTaxonomy($response->body);

        return $taxonomy;
    }

    protected function initRequestTemplate()
    {
        $template = Request::init()
        ->method(Http::GET)
        ->mime('json')
        ->expectsJson();

        $template->_debug = $this->debugRequests;

        if (!is_null($this->previewApiKey)) {
            $template->addHeader('Authorization', 'Bearer '.$this->previewApiKey);
        }
        if ($this->waitForLoadingNewContent) {
            $template->addHeader('X-KC-Wait-For-Loading-New-Content', 'true');
        }

        // Set an HTTP request template
        Request::ini($template);
    }

    protected function sendRequest($uri)
    {
        $request = Request::get($uri);
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
