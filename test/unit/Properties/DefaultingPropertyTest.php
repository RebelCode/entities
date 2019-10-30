<?php

namespace RebelCode\Entities\UnitTests\Properties;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\StoreInterface;
use RebelCode\Entities\Properties\DefaultingProperty;
use RebelCode\Entities\Properties\Property;

/**
 * @since [*next-version*]
 */
class DefaultingPropertyTest extends TestCase
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
        $subject = new DefaultingProperty([]);

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
    public function testGetValueDefault()
    {
        {
            $key1 = uniqid('key1');
            $key2 = uniqid('key2');
            $key3 = uniqid('key3');

            $val = uniqid('val');

            $store = $this->createStore();
            $store->expects($this->exactly(3))
                  ->method('get')
                  ->withConsecutive([$key1], [$key2], [$key3])
                  ->willReturnCallback(function ($key) use ($key3, $val) {
                      if ($key === $key3) {
                          return $val;
                      }

                      throw new OutOfBoundsException();
                  });

            $entity = $this->createEntity();
            $entity->expects($this->atLeastOnce())->method('getStore')->willReturn($store);
        }

        $subject = new DefaultingProperty([$key1, $key2, $key3]);

        static::assertSame($val, $subject->getValue($entity));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValueMissing()
    {
        {
            $key1 = uniqid('key1');
            $key2 = uniqid('key2');
            $key3 = uniqid('key3');

            $store = $this->createStore();
            $store->expects($this->exactly(3))
                  ->method('get')
                  ->withConsecutive([$key1], [$key2], [$key3])
                  ->willThrowException(new OutOfBoundsException());

            $entity = $this->createEntity();
            $entity->expects($this->atLeastOnce())->method('getStore')->willReturn($store);
        }

        $subject = new DefaultingProperty([$key1, $key2, $key3]);

        $this->setExpectedException('OutOfBoundsException');

        $subject->getValue($entity);
    }

    /**
     * @since [*next-version*]
     */
    public function testSetValue()
    {
        {
            $key1 = uniqid('key1');
            $key2 = uniqid('key2');
            $key3 = uniqid('key3');

            $val = uniqid('val');

            $entity = $this->createEntity();
        }

        $subject = new DefaultingProperty([$key1, $key2, $key3]);

        $commit = [$key1 => $val];

        static::assertSame($commit, $subject->setValue($entity, $val));
    }
}
