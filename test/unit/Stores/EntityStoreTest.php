<?php

namespace RebelCode\Entities\UnitTests\Stores;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Stores\EntityStore;

/**
 * @since [*next-version*]
 */
class EntityStoreTest extends TestCase
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
        $subject = new EntityStore($this->createEntity());

        static::assertInstanceOf('RebelCode\Entities\Api\StoreInterface', $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGet()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');

            $entity = $this->createEntity();
            $entity->expects($this->once())->method('get')->with($key)->willReturn($val);
        }
        {
            $subject = new EntityStore($entity);
        }

        static::assertSame($val, $subject->get($key));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetMissing()
    {
        {
            $key = uniqid('missing');

            $entity = $this->createEntity();
            $entity->expects($this->once())->method('get')->with($key)->willThrowException(new OutOfBoundsException());
        }
        {
            $subject = new EntityStore($entity);
        }

        static::setExpectedException('OutOfBoundsException');

        $subject->get($key);
    }

    /**
     * @since [*next-version*]
     */
    public function testHas()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');

            $entity = $this->createEntity();
            $entity->expects($this->once())->method('get')->with($key)->willReturn($val);
        }
        {
            $subject = new EntityStore($entity);
        }

        static::assertTrue($subject->has($key));
    }

    /**
     * @since [*next-version*]
     */
    public function testHasMissing()
    {
        {
            $key = uniqid('missing');

            $entity = $this->createEntity();
            $entity->expects($this->once())->method('get')->with($key)->willThrowException(new OutOfBoundsException());
        }
        {
            $subject = new EntityStore($entity);
        }

        static::assertFalse($subject->has($key));
    }

    /**
     * @since [*next-version*]
     */
    public function testSet()
    {
        {
            $key1 = uniqid('key1');
            $val1 = uniqid('val1');
            $key2 = uniqid('key2');
            $val2 = uniqid('val2');

            $commit = [
                $key1 => $val1,
                $key2 => $val2,
            ];
        }
        {
            $entity = $this->createEntity();
            $entity->expects($this->once())->method('set')->with($commit);
        }
        {
            $subject = new EntityStore($entity);
        }

        $store = $subject->set($commit);

        static::assertInstanceOf('RebelCode\Entities\Api\StoreInterface', $store);
    }
}
