<?php

namespace KenticoCloud\Tests\Unit;

use KenticoCloud\Delivery\DefaultMapper;
use PHPUnit\Framework\TestCase;

class DefaultMapperTest extends TestCase
{
    public function testGetTypeClassNull()
    {
        // Arrange
        $mapper = new DefaultMapper();

        // Act
        $type = $mapper->getTypeClass(null);

        // Assert
        $this->assertNull($type);
    }
}
