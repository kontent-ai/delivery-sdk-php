<?php
/**
 * Executes requests against the Kentico Cloud Delivery API.
 */

namespace KenticoCloud\Delivery;

use Httpful\Request;
use Httpful\Http;
use KenticoCloud\Delivery\Models\Shared\Pagination;

/**
 * Class DeliveryClient - executes requests against the Kentico Cloud Delivery API.
 */
class DeliveryClient
{
    private $sdkVersion = '0.9.5';

    private $urlBuilder = null;
    private $previewMode = false;
    protected $previewApiKey = null;
    protected $debugRequests = false;
    protected $waitForLoadingNewContent = false;
    protected $contentTypeFactory = null;
    protected $taxonomyFactory = null;

    /**
     * Gets or sets TypeMapperInterface which serves for resolving strong types based on provided information.
     *
     * @var TypeMapperInterface
     */
    public $typeMapper = null;

    /**
     * Gets or sets PropertyMapperInterface which serves for mapping model properties to data in JSON responses.
     *
     * @var PropertyMapperInterface
     */
    public $propertyMapper = null;

    /**
     * Gets or sets ValueConverterInterface which serves for converting simple values to desired types.
     *
     * @var ValueConverterInterface
     */
    public $valueConverter = null;

    /**
     * Gets or sets ContentLinkUrlResolverInterface resolve links URLs.
     *
     * @var ContentLinkUrlResolverInterface
     */
    public $contentLinkUrlResolver = null;

    /**
     *  Serves for converting inline modular content to desired html.
     *
     * @var InlineModularContentResolverInterface|null
     */
    public $inlineModularContentResolver = null;

    /**
     * Gets or sets ModelBinder which serves for binding of JSON responses to defined content item models.
     *
     * @var ModelBinder
     */
    public $modelBinder = null;

    /**
     * Creates a new instance of DeliveryClient.
     *
     * @param string $projectId                Kentico Cloud Delivery API Project ID
     * @param string $previewApiKey            Kentico Cloud Delivery API Preview API key
     * @param bool   $waitForLoadingNewContent Gets whether you want to wait for updated content. (Useful for webhooks.)
     * @param bool   $debugRequests            Switches the HTTP client to debug mode
     */
    public function __construct(string $projectId, string $previewApiKey = null, bool $waitForLoadingNewContent = null, bool $debugRequests = null)
    {
        $this->previewApiKey = $previewApiKey;
        $this->previewMode = !is_null($previewApiKey);
        $this->urlBuilder = new UrlBuilder($projectId, $this->previewMode);
        $this->waitForLoadingNewContent = $waitForLoadingNewContent ?? $this->waitForLoadingNewContent;
        $this->debugRequests = $debugRequests ?? $this->debugRequests;
        $this->initRequestTemplate();
    }

    /**
     * Retrieves content items.
     *
     * @param $params query parameters adjusting the retrieved data (filtering, sorting and other params)
     *
     * @return mixed object with an array of content items and pagination information
     */
    public function getItems($params = null)
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

    /**
     * Retrieves a content item.
     *
     * @param string $codename codename of the content item to be retrieved
     * @param $params query parameters adjusting the retrieved data (filtering, sorting and other params)
     *
     * @return mixed a content item
     */
    public function getItem($codename, $params = null)
    {
        $uri = $this->urlBuilder->getItemUrl($codename, $params);
        $response = $this->sendRequest($uri);

        $binder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        if (!isset($properties['item']) || !count(get_object_vars($properties['item']))) {
            return null;
        }

        // Bind content item
        $item = $binder->getContentItem($properties['item'], $properties['modular_content']);

        return $item;
    }

    /**
     * Retrieves a single element of a content type.
     *
     * @param string $typeCodename    codename of a content type
     * @param string $elementCodename codename of an element
     */
    public function getElement($typeCodename, $elementCodename)
    {
        $uri = $this->urlBuilder->getContentElementUrl($typeCodename, $elementCodename);
        $response = $this->sendRequest($uri);

        $typeFactory = $this->getContentTypeFactory();

        // Bind content type element
        $element = $typeFactory->createElement($response->body, $elementCodename);

        return $element;
    }

    /**
     * Retrieves Content Types.
     *
     * @param $params Specification of parameters for Content Types retrieval
     *
     * @return mixed array of corresponding content type objects
     */
    public function getTypes($params = null)
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
     * Retrieves a content type.
     *
     * @param $codename codename of a content type to be retrieved
     *
     * @return content type
     */
    public function getType($codename)
    {
        $uri = $this->urlBuilder->getTypeUrl($codename);
        $response = $this->sendRequest($uri);

        $typeFactory = $this->getContentTypeFactory();

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

        $template->addHeader('X-KC-SDKID:', "packagist.org;kentico-cloud/delivery-sdk-php;{$this->sdkVersion}");

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
            if ($this->typeMapper == null || $this->propertyMapper == null || $this->valueConverter == null || $this->contentLinkUrlResolver == null) {
                $defaultMapper = new DefaultMapper();
            }
            $this->modelBinder = new ModelBinder($this->typeMapper ?? $defaultMapper, $this->propertyMapper ?? $defaultMapper, $this->valueConverter ?? $defaultMapper);
            $this->modelBinder->contentLinkUrlResolver = $this->contentLinkUrlResolver ?? $defaultMapper;
            $this->modelBinder->inlineModularContentResolver = $this->inlineModularContentResolver ?? $defaultMapper;
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
