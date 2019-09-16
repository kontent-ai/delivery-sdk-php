<?php

namespace Kentico\Kontent\Tests\Unit;

use Kentico\Kontent\Delivery\Helpers\TextHelper;
use PHPUnit\Framework\TestCase;

class TextHelperTest extends TestCase
{
    public function testPascalCase()
    {
        $pascalCase = TextHelper::getInstance()->pascalCase('Pascal_Case', '_');
        $this->assertEquals('PascalCase', $pascalCase);
    }

    public function testCamelCase()
    {
        $camelCase = TextHelper::getInstance()->camelCase('Camel_Case', '_');
        $this->assertEquals('camelCase', $camelCase);
    }

    public function testDecamelize()
    {
        $decamelized = TextHelper::getInstance()->decamelize('camelCase', '*');
        $this->assertEquals('camel*case', $decamelized);
    }
}
