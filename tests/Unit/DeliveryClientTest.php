<?php

namespace Kentico\Kontent\Tests\Unit;

use Kentico\Kontent\Delivery\DeliveryClient;
use PHPUnit\Framework\TestCase;
use Httpful\Request;
use InvalidArgumentException;

class DeliveryClientTest extends TestCase
{
    public function getProjectId()
    {
        return '975bf280-fd91-488c-994c-2f04416e5ee3';
    }

    public function test_ctor_securityAndProductionSet_ExceptionThrown()
    {
        $securityKey = 'securityKey';
        $previewKey = 'previewKey';

        $this->expectException(InvalidArgumentException::class);
        $client = new DeliveryClient($this->getProjectId(), $previewKey, $securityKey);
    }

    public function test_ctor_previewKeyIsSetInHeaders()
    {
        $previewKey = 'previewKey';
        $client = new DeliveryClient($this->getProjectId(), 'previewKey');
        $authorizationHeaderValue = Request::d('headers')['Authorization'];

        $this->assertEquals('Bearer '.$previewKey, $authorizationHeaderValue);
    }

    public function test_ctor_securityKeyIsSetInHeaders()
    {
        $securityKey = 'securityKey';
        $client = new DeliveryClient($this->getProjectId(), null, $securityKey);
        $authorizationHeaderValue = Request::d('headers')['Authorization'];

        $this->assertEquals('Bearer '.$securityKey, $authorizationHeaderValue);
    }
}
