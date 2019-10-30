<?php

namespace RebelCode\Entities\UnitTests\Properties;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Properties\StaticProperty;

/**
 * @since [*next-version*]
 */
class StaticPropertyTest extends TestCase
{
    /**
     * @since [*next-version*]
     *
     * @return EntityInterface|MockObject
     */
    protected function createEntity()
    {
        return $this->getMockForAbstractClass('RebelCode\Entities\Api\EntityInterface');
    }

    /**
     * @since [*next-version*]
     */
    public function testConstruct()
    {
        $subject = new StaticProperty(null);

        static::assertInstanceOf('RebelCode\Entities\Properties\StaticProperty', $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValue()
    {
        {
            $value = uniqid('value');
        }
        {
            $entity = $this->createEntity();
            $subject = new StaticProperty($value);
        }

        static::assertSame($value, $subject->getValue($entity));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetValue()
    {
        {
            $entity = $this->createEntity();
            $subject = new StaticProperty(uniqid('value'));
        }

        static::assertEmpty($subject->setValue($entity, uniqid('value2')));
    }
}
