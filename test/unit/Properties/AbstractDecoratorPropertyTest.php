<?php

namespace RebelCode\Entities\UnitTests\Properties;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Properties\AbstractDecoratorProperty;

/**
 * @since [*next-version*]
 */
class AbstractDecoratorPropertyTest extends TestCase
{
    /**
     * @since [*next-version*]
     *
     * @param PropertyInterface $property
     *
     * @return MockObject|AbstractDecoratorProperty
     */
    protected function createSubject(PropertyInterface $property)
    {
        return $this->getMockBuilder('RebelCode\Entities\Properties\AbstractDecoratorProperty')
                    ->setConstructorArgs([$property])
                    ->getMockForAbstractClass();
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
        $subject = $this->createSubject($this->createProperty());

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

            $subject = $this->createSubject($prop);
            $subject->expects(static::once())->method('getter')->with($entity, $val)->willReturn($val2);
        }

        static::assertSame($val2, $subject->getValue($entity));
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

            $prop = $this->createProperty();
            $prop->expects($this->once())->method('setValue')->with($entity, $val2)->willReturn($expected);
        }
        {
            $subject = $this->createSubject($prop);
            $subject->expects(static::once())->method('setter')->with($entity, $val)->willReturn($val2);
        }

        static::assertSame($expected, $subject->setValue($entity, $val));
    }
}
