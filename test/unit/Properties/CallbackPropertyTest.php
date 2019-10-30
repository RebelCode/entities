<?php

namespace RebelCode\Entities\UnitTests\Properties;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Properties\CallbackProperty;

/**
 * @since [*next-version*]
 */
class CallbackPropertyTest extends TestCase
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
        $subject = new CallbackProperty();

        static::assertInstanceOf('RebelCode\Entities\Api\PropertyInterface', $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValue()
    {
        {
            $val = uniqid('val');
            $entity = $this->createEntity();

            $getter = function ($argEntity) use ($val, $entity) {
                static::assertSame($entity, $argEntity);

                return $val;
            };
        }
        {
            $subject = new CallbackProperty($getter);
        }

        static::assertSame($val, $subject->getValue($entity));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValueNoGetter()
    {
        $entity = $this->createEntity();
        $subject = new CallbackProperty(null);

        static::assertNull($subject->getValue($entity));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetValue()
    {
        {
            $val = uniqid('val');
            $commit = [uniqid('key') => $val];
            $entity = $this->createEntity();

            $setter = function ($argEntity, $argVal) use ($val, $entity, $commit) {
                static::assertSame($entity, $argEntity);
                static::assertSame($val, $argVal);

                return $commit;
            };
        }
        {
            $subject = new CallbackProperty(null, $setter);
        }

        static::assertSame($commit, $subject->setValue($entity, $val));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetValueNoSetter()
    {
        $val = uniqid('val');
        $entity = $this->createEntity();
        $subject = new CallbackProperty(null, null);

        static::assertEmpty($subject->setValue($entity, $val));
    }
}
