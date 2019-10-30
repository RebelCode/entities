<?php

namespace RebelCode\Entities\UnitTests\Properties;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Properties\CallbackDecoratorProperty;

/**
 * @since [*next-version*]
 */
class CallbackDecoratorPropertyTest extends TestCase
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
     *
     * @return PropertyInterface|MockObject
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
        $subject = new CallbackDecoratorProperty($this->createProperty());

        static::assertInstanceOf('RebelCode\Entities\Api\PropertyInterface', $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValue()
    {
        {
            $entity = $this->createEntity();
        }
        {
            $val = uniqid('val');

            $prop = $this->createProperty();
            $prop->expects($this->once())->method('getValue')->with($entity)->willReturn($val);
        }
        {
            $val2 = uniqid('val2');

            $getter = function ($argEntity, $argVal) use ($val, $val2, $entity) {
                static::assertSame($entity, $argEntity);
                static::assertSame($val, $argVal);

                return $val2;
            };
        }
        {
            $subject = new CallbackDecoratorProperty($prop, $getter);
        }

        static::assertSame($val2, $subject->getValue($entity));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetValueNoGetter()
    {
        {
            $entity = $this->createEntity();
        }
        {
            $val = uniqid('val');

            $prop = $this->createProperty();
            $prop->expects($this->once())->method('getValue')->with($entity)->willReturn($val);
        }
        {
            $subject = new CallbackDecoratorProperty($prop, null);
        }

        static::assertSame($val, $subject->getValue($entity));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetValue()
    {
        {
            $entity = $this->createEntity();
        }
        {
            $key = uniqid('val');
            $val = uniqid('val');
            $val2 = uniqid('val2');

            $expected = [$key => $val2];
        }
        {
            $setter = function ($argEntity, $argVal) use ($val, $val2, $entity) {
                static::assertSame($entity, $argEntity);
                static::assertSame($val, $argVal);

                return $val2;
            };

            $prop = $this->createProperty();
            $prop->expects($this->once())->method('setValue')->with($entity, $val2)->willReturn($expected);
        }
        {
            $subject = new CallbackDecoratorProperty($prop, null, $setter);
        }

        static::assertSame($expected, $subject->setValue($entity, $val));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetValueNoSetter()
    {
        {
            $entity = $this->createEntity();
        }
        {
            $key = uniqid('val');
            $val = uniqid('val');

            $expected = [$key => $val];
        }
        {
            $prop = $this->createProperty();
            $prop->expects($this->once())->method('setValue')->with($entity, $val)->willReturn($expected);
        }
        {
            $subject = new CallbackDecoratorProperty($prop, null, null);
        }

        static::assertSame($expected, $subject->setValue($entity, $val));
    }
}
