<?php

namespace RebelCode\Entities\UnitTests\Properties;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\StoreInterface;
use RebelCode\Entities\Properties\Property;

/**
 * @since [*next-version*]
 */
class PropertyTest extends TestCase
{
    /**
     * @since [*next-version*]
     *
     * @return StoreInterface|MockObject
     */
    protected function createStore()
    {
        return $this->getMockForAbstractClass('RebelCode\Entities\Api\StoreInterface');
    }

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
        $subject = new Property('');

        static::assertInstanceOf('RebelCode\Entities\Api\PropertyInterface', $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValue()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');

            $store = $this->createStore();
            $store->expects($this->once())->method('get')->with($key)->willReturn($val);

            $entity = $this->createEntity();
            $entity->expects($this->atLeastOnce())->method('getStore')->willReturn($store);
        }

        $subject = new Property($key);

        static::assertSame($val, $subject->getValue($entity));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValueMissing()
    {
        {
            $key = uniqid('key');

            $store = $this->createStore();
            $store->expects($this->once())->method('get')->with($key)->willThrowException(new OutOfBoundsException());

            $entity = $this->createEntity();
            $entity->expects($this->atLeastOnce())->method('getStore')->willReturn($store);
        }

        $subject = new Property($key);

        $this->setExpectedException('OutOfBoundsException');

        $subject->getValue($entity);
    }

    /**
     * @since [*next-version*]
     */
    public function testSetValue()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');

            $entity = $this->createEntity();
        }

        $subject = new Property($key);

        $commit = [$key => $val];

        static::assertSame($commit, $subject->setValue($entity, $val));
    }
}
