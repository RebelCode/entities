<?php

namespace RebelCode\Entities\UnitTests\Schemas;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Schemas\Schema;

/**
 * @since [*next-version*]
 */
class SchemaTest extends TestCase
{
    /**
     * @since [*next-version*]
     *
     * @return MockObject|PropertyInterface
     */
    protected function createProperty()
    {
        return $this->getMockForAbstractClass('RebelCode\Entities\Api\PropertyInterface');
    }

    /**
     * @since [*next-version*]
     */
    public function testConstruct()
    {
        $subject = new Schema([], []);

        static::assertInstanceOf('RebelCode\Entities\Api\SchemaInterface', $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetProperties()
    {
        $properties = [
            uniqid('prop1') => $this->createProperty(),
            uniqid('prop2') => $this->createProperty(),
            uniqid('prop2') => $this->createProperty(),
        ];

        $subject = new Schema($properties, []);

        static::assertSame($properties, $subject->getProperties());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefaults()
    {
        $defaults = [
            uniqid('prop1') => uniqid('default1'),
            uniqid('prop2') => uniqid('default2'),
            uniqid('prop2') => uniqid('default3'),
        ];

        $subject = new Schema([], $defaults);

        static::assertSame($defaults, $subject->getDefaults());
    }
}
