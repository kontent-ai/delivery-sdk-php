<?php
/**
 * Executes requests against the Kentico Kontent Delivery API.
 */

namespace Kentico\Kontent\Delivery;

use Httpful\Request;
use Httpful\Http;
use Kentico\Kontent\Delivery\Models\Shared\Pagination;
use InvalidArgumentException;
use Exception;

/**
 * Class DeliveryClient - executes requests against the Kentico Kontent Delivery API.
 */
class DeliveryClient
{
    private $sdkVersion = '4.0.0';

    private $urlBuilder = null;
    private $previewMode = false;
    private $securedMode = false;
    protected $previewApiKey = null;
    protected $securedProductionApiKey = null;
    protected $waitForLoadingNewContent = false;
    protected $contentTypeFactory = null;
    protected $taxonomyFactory = null;
    protected $languagesFactory = null;
    protected $debugRequests = false;
    protected $retryAttempts = 0;

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
     *  Serves for converting inline linked items to desired html.
     *
     * @var InlineLinkedItemsResolverInterface|null
     */
    public $inlineLinkedItemsResolver = null;

    /**
     * Gets or sets ModelBinder which serves for binding of JSON responses to defined content item models.
     *
     * @var ModelBinder
     */
    public $modelBinder = null;

    /**
     * Creates a new instance of DeliveryClient.
     *
     * @param string $projectId                Kentico Kontent Delivery API Project ID
     * @param string $previewApiKey            Kentico Kontent Delivery API Preview API key
     * @param string $securedProductionApiKey  Kentico Kontent Delivery API Secured production API key
     * @param bool   $waitForLoadingNewContent Gets whether you want to wait for updated content. (Useful for webhooks.)
     * @param bool   $debugRequests            Switches the HTTP client to debug mode
     * @param int    $retryAttempts            Number of times the client will retry to connect to the Kentico Kontent API on failures per request
     */
    public function __construct(string $projectId, string $previewApiKey = null, string $securedProductionApiKey = null, bool $waitForLoadingNewContent = null, bool $debugRequests = null, int $retryAttempts = null)
    {
        $this->previewApiKey = $previewApiKey;
        $this->previewMode = !is_null($previewApiKey);
        $this->urlBuilder = new UrlBuilder($projectId, $this->previewMode);
        $this->securedProductionApiKey = $securedProductionApiKey;
        $this->securedMode = !is_null($securedProductionApiKey);
        $this->waitForLoadingNewContent = $waitForLoadingNewContent ?? $this->waitForLoadingNewContent;
        $this->debugRequests = $debugRequests ?? $this->debugRequests;
        $this->retryAttempts = $retryAttempts ?? $this->retryAttempts;
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

    public function getAllModularItems($params = null)
    {
        $uri = $this->urlBuilder->getItemsUrl($params);
        $response = $this->sendRequest($uri);

        $binder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        // Items
        $items = $binder->getModularItems($properties['items'], $properties['modular_content']);

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

    /**
     * Retrieves Languages.
     *
     * @param $params queryParams Specification of parameters for Language retrieval
     *
     * @return \Kentico\Kontent\Delivery\Models\Languages\LanguagesResponse::class of retrieved Languages
     */
    public function getLanguages($params = null)
    {
        $uri = $this->urlBuilder->getLanguagesUrl($params);
        $response = $this->sendRequest($uri);

        $factory = $this->getLanguageFactory();

        $binder = $this->getModelBinder();

        $properties = get_object_vars($response->body);

        // Taxonomies
        $languages = $factory->createLanguages($response->body);

        // Pagination
        $pagination = $binder->bindModel(Pagination::class, $properties[Pagination::PAGINATION_ELEMENT_NAME]);

        $languagesResponse = new Models\Languages\LanguagesResponse($languages, $pagination);

        return $languagesResponse;
    }

    protected function initRequestTemplate()
    {
        if ($this->previewMode && $this->securedMode) {
            throw new InvalidArgumentException('Preview API key and Secured production API key must not be configured at the same time.');
        }

        $template = Request::init()
        ->method(Http::GET)
        ->mime('json')
        ->expectsJson();

        $template->_debug = $this->debugRequests;

        if ($this->previewMode) {
            $template->addHeader('Authorization', 'Bearer '.$this->previewApiKey);
        }
        if ($this->securedMode) {
            $template->addHeader('Authorization', 'Bearer '.$this->securedProductionApiKey);
        }
        if ($this->waitForLoadingNewContent) {
            $template->addHeader('X-KC-Wait-For-Loading-New-Content', 'true');
        }

        $template->addHeader('X-KC-SDKID', "packagist.org;kentico/kontent-delivery-sdk-php;{$this->sdkVersion}");

        // Set an HTTP request template
        Request::ini($template);
    }

    protected function sendRequest(string $uri, int $attemptNumber = 0)
    {
        $request = Request::get($uri);
        try {
            $response = $request->send();
            $this->lastRequest = $request;
            $this->lastResponse = $response;

            return $response;
        } catch (Exception $e) {
            if ($attemptNumber < $this->retryAttempts) {
                $nextAttemptNumber = $attemptNumber + 1;
                // Perform a binary exponential backoff
                $wait = 100 * pow(2, $nextAttemptNumber);
                usleep($wait * 1000);

                return $this->sendRequest($uri, $nextAttemptNumber);
            } else {
                throw $e;
            }
        }
    }

    protected function getModelBinder()
    {
        if ($this->modelBinder == null) {
            if ($this->typeMapper == null || $this->propertyMapper == null || $this->valueConverter == null || $this->contentLinkUrlResolver == null) {
                $defaultMapper = new DefaultMapper();
            }
            $this->modelBinder = new ModelBinder($this->typeMapper ?? $defaultMapper, $this->propertyMapper ?? $defaultMapper, $this->valueConverter ?? $defaultMapper);
            $this->modelBinder->contentLinkUrlResolver = $this->contentLinkUrlResolver ?? $defaultMapper;
            $this->modelBinder->inlineLinkedItemsResolver = $this->inlineLinkedItemsResolver ?? $defaultMapper;
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

    /**
     * @return LanguageFactory
     */
    protected function getLanguageFactory()
    {
        if ($this->languagesFactory == null) {
            $this->languagesFactory = new LanguageFactory();
        }

        return $this->languagesFactory;
    }
}
