<?php

namespace RebelCode\Entities\UnitTests;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Api\SchemaInterface;
use RebelCode\Entities\Api\StoreInterface;
use RebelCode\Entities\Entity;

/**
 * @since [*next-version*]
 */
class EntityTest extends TestCase
{
    /**
     * @since [*next-version*]
     *
     * @return MockObject|StoreInterface
     */
    protected function createStore()
    {
        return $this->getMockForAbstractClass('RebelCode\Entities\Api\StoreInterface');
    }

    /**
     * @since [*next-version*]
     *
     * @return MockObject|PropertyInterface
     */
    protected function createProp()
    {
        return $this->getMockForAbstractClass('RebelCode\Entities\Api\PropertyInterface');
    }

    /**
     * @since [*next-version*]
     *
     * @return MockObject|SchemaInterface
     */
    protected function createSchema()
    {
        return $this->getMockForAbstractClass('RebelCode\Entities\Api\SchemaInterface');
    }

    /**
     * @since [*next-version*]
     */
    public function testConstruct()
    {
        $subject = new Entity($this->createSchema(), $this->createStore());

        static::assertInstanceOf('RebelCode\Entities\Api\EntityInterface', $subject);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetSchema()
    {
        $schema = $this->createSchema();
        $subject = new Entity($schema, $this->createStore());

        static::assertSame($schema, $subject->getSchema());
    }

    /**
     * @since [*next-version*]
     */
    public function testGetStore()
    {
        $store = $this->createStore();
        $subject = new Entity($this->createSchema(), $store);

        static::assertSame($store, $subject->getStore());
    }

    /**
     * @since [*next-version*]
     */
    public function testGet()
    {
        $propKey = uniqid('key');
        $propVal = uniqid('value');
        $prop = $this->createProp();

        $schema = $this->createSchema();
        $schema->expects(static::once())
               ->method('getProperties')
               ->willReturn([$propKey => $prop]);

        $store = $this->createStore();
        $subject = new Entity($schema, $store);

        $prop->expects(static::once())
             ->method('getValue')
             ->with($subject)
             ->willReturn($propVal);

        static::assertSame($propVal, $subject->get($propKey));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetNoProp()
    {
        $propKey = uniqid('key');

        $schema = $this->createSchema();
        $schema->expects(static::once())
               ->method('getProperties')
               ->willReturn([]);

        $store = $this->createStore();
        $subject = new Entity($schema, $store);

        static::setExpectedException('OutOfBoundsException');

        $subject->get($propKey);
    }

    /**
     * @since [*next-version*]
     */
    public function testGetDefault()
    {
        $propKey = uniqid('key');
        $propDef = uniqid('value');
        $prop = $this->createProp();

        $schema = $this->createSchema();
        $schema->expects(static::once())
               ->method('getProperties')
               ->willReturn([$propKey => $prop]);
        $schema->expects(static::once())
               ->method('getDefaults')
               ->willReturn([$propKey => $propDef]);

        $store = $this->createStore();
        $subject = new Entity($schema, $store);

        $prop->expects(static::once())
             ->method('getValue')
             ->with($subject)
             ->willThrowException(new OutOfBoundsException());

        static::assertSame($propDef, $subject->get($propKey));
    }

    /**
     * @since [*next-version*]
     */
    public function testSet()
    {
        {
            $propKey = uniqid('key');
            $newVal = uniqid('new-value');
            $changeSet = [
                uniqid('some-key') => uniqid('some-val'),
                uniqid('some-key-2') => uniqid('some-val-2'),
            ];
        }
        {
            $prop = $this->createProp();

            $store = $this->createStore();

            $schema = $this->createSchema();
            $schema->expects(static::once())
                   ->method('getProperties')
                   ->willReturn([$propKey => $prop]);

            $subject = new Entity($schema, $store);
        }
        {
            $prop->expects(static::once())
                 ->method('setValue')
                 ->with($subject, $newVal)
                 ->willReturn($changeSet);

            $store2 = $this->createStore();
            $store->expects(static::once())
                  ->method('set')
                  ->with($changeSet)
                  ->willReturn($store2);
        }

        $result = $subject->set([$propKey => $newVal]);

        static::assertInstanceOf('RebelCode\Entities\Api\EntityInterface', $result);
        static::assertSame($store2, $result->getStore());
    }

    /**
     * @since [*next-version*]
     */
    public function testSetMultiple()
    {
        {
            $propKey1 = uniqid('key-1');
            $propKey2 = uniqid('key-2');
            $newVal1 = uniqid('new-value-1');
            $newVal2 = uniqid('new-value-2');
            $changeSet1 = [
                uniqid('some-key') => uniqid('some-val'),
                uniqid('some-key-2') => uniqid('some-val-2'),
            ];
            $changeSet2 = [
                uniqid('some-key-3') => uniqid('some-val-3'),
            ];
            $changeSetFull = array_merge($changeSet1, $changeSet2);
        }
        {
            $prop1 = $this->createProp();
            $prop2 = $this->createProp();

            $store = $this->createStore();
            $store2 = $this->createStore();

            $schema = $this->createSchema();
            $schema->expects(static::once())
                   ->method('getProperties')
                   ->willReturn([
                       $propKey1 => $prop1,
                       $propKey2 => $prop2,
                   ]);

            $subject = new Entity($schema, $store);
        }
        {
            $prop1->expects(static::once())
                  ->method('setValue')
                  ->with($subject, $newVal1)
                  ->willReturn($changeSet1);
            $prop2->expects(static::once())
                  ->method('setValue')
                  ->with($subject, $newVal2)
                  ->willReturn($changeSet2);

            $store->expects(static::once())
                  ->method('set')
                  ->with($changeSetFull)
                  ->willReturn($store2);
        }

        $result = $subject->set([
            $propKey1 => $newVal1,
            $propKey2 => $newVal2,
        ]);

        static::assertInstanceOf('RebelCode\Entities\Api\EntityInterface', $result);
        static::assertSame($schema, $result->getSchema());
        static::assertSame($store2, $result->getStore());
    }

    /**
     * @since [*next-version*]
     */
    public function testSetNoProp()
    {
        {
            $propKey = uniqid('key');
            $newVal = uniqid('new-value');
        }
        {
            $store = $this->createStore();
            $schema = $this->createSchema();
            $schema->expects(static::once())
                   ->method('getProperties')
                   ->willReturn([]);

            $subject = new Entity($schema, $store);
        }
        {
            $store->expects(static::never())
                  ->method('set');
        }

        static::setExpectedException('OutOfBoundsException');

        $subject->set([$propKey => $newVal]);
    }

    /**
     * @since [*next-version*]
     */
    public function testSetError()
    {
        {
            $propKey1 = uniqid('key-1');
            $propKey2 = uniqid('key-2');
            $propKey3 = uniqid('key-3');
            $newVal1 = uniqid('new-value-1');
            $newVal2 = uniqid('new-value-2');
            $newVal3 = uniqid('new-value-3');

            $changeSet1 = [
                uniqid('cs-key-1') => uniqid('cs-val-1'),
                uniqid('cs-key-2') => uniqid('cs-val-2'),
            ];
            $changeSet3 = [
                uniqid('cs-key-3') => uniqid('cs-val-3'),
                uniqid('cs-key-4') => uniqid('cs-val-4'),
            ];
            $changeSetFull = array_merge($changeSet1, $changeSet3);
        }
        {
            $prop1 = $this->createProp();
            $prop2 = $this->createProp();
            $prop3 = $this->createProp();

            $store = $this->createStore();
            $store2 = $this->createStore();

            $schema = $this->createSchema();
            $schema->expects(static::once())
                   ->method('getProperties')
                   ->willReturn([
                       $propKey1 => $prop1,
                       $propKey2 => $prop2,
                       $propKey3 => $prop3,
                   ]);

            $subject = new Entity($schema, $store);
        }
        {
            $prop1->expects(static::once())
                  ->method('setValue')
                  ->with($subject, $newVal1)
                  ->willReturn($changeSet1);

            $prop2->expects(static::once())
                  ->method('setValue')
                  ->with($subject, $newVal2)
                  ->willThrowException(new OutOfBoundsException());

            $prop3->expects(static::once())
                  ->method('setValue')
                  ->with($subject)
                  ->willReturn($changeSet3);

            $store->expects(static::once())
                  ->method('set')
                  ->with($changeSetFull)
                  ->willReturn($store2);
        }

        $result = $subject->set([
            $propKey1 => $newVal1,
            $propKey2 => $newVal2,
            $propKey3 => $newVal3,
        ]);

        static::assertInstanceOf('RebelCode\Entities\Api\EntityInterface', $result);
        static::assertSame($schema, $result->getSchema());
        static::assertSame($store2, $result->getStore());
    }

    /**
     * @since [*next-version*]
     */
    public function testExport()
    {
        {
            $propKey1 = uniqid('key-1');
            $propKey2 = uniqid('key-2');
            $propKey3 = uniqid('key-3');
            $propVal1 = uniqid('val-1');
            $propVal2 = uniqid('val-2');
            $propVal3 = uniqid('val-3');
        }
        {
            $prop1 = $this->createProp();
            $prop2 = $this->createProp();
            $prop3 = $this->createProp();

            $schema = $this->createSchema();
            $store = $this->createStore();

            $schema->expects(static::atLeastOnce())
                   ->method('getProperties')
                   ->willReturn([
                       $propKey1 => $prop1,
                       $propKey2 => $prop2,
                       $propKey3 => $prop3,
                   ]);

            $subject = new Entity($schema, $store);
        }
        {
            $prop1->expects(static::once())
                  ->method('getValue')
                  ->with($subject)
                  ->willReturn($propVal1);

            $prop2->expects(static::once())
                  ->method('getValue')
                  ->with($subject)
                  ->willReturn($propVal2);

            $prop3->expects(static::once())
                  ->method('getValue')
                  ->with($subject)
                  ->willReturn($propVal3);
        }

        $expected = [
            $propKey1 => $propVal1,
            $propKey2 => $propVal2,
            $propKey3 => $propVal3,
        ];

        static::assertEquals($expected, $subject->export());
    }

    /**
     * @since [*next-version*]
     */
    public function testExportWithDefaults()
    {
        {
            $propKey1 = uniqid('key-1');
            $propKey2 = uniqid('key-2');
            $propKey3 = uniqid('key-3');
            $propVal1 = uniqid('val-1');
            $propVal3 = uniqid('val-3');
            $propDef1 = uniqid('def-1');
            $propDef2 = uniqid('def-2');
            $propDef3 = uniqid('def-3');
        }
        {
            $prop1 = $this->createProp();
            $prop2 = $this->createProp();
            $prop3 = $this->createProp();

            $schema = $this->createSchema();
            $store = $this->createStore();

            $schema->expects(static::atLeastOnce())
                   ->method('getProperties')
                   ->willReturn([
                       $propKey1 => $prop1,
                       $propKey2 => $prop2,
                       $propKey3 => $prop3,
                   ]);
            $schema->expects(static::atLeastOnce())
                   ->method('getDefaults')
                   ->willReturn([
                       $propKey1 => $propDef1,
                       $propKey2 => $propDef2,
                       $propKey3 => $propDef3,
                   ]);

            $subject = new Entity($schema, $store);
        }
        {
            $prop1->expects(static::once())
                  ->method('getValue')
                  ->with($subject)
                  ->willReturn($propVal1);

            $prop2->expects(static::once())
                  ->method('getValue')
                  ->with($subject)
                  ->willThrowException(new OutOfBoundsException());

            $prop3->expects(static::once())
                  ->method('getValue')
                  ->with($subject)
                  ->willReturn($propVal3);
        }

        $expected = [
            $propKey1 => $propVal1,
            $propKey2 => $propDef2,
            $propKey3 => $propVal3,
        ];

        static::assertEquals($expected, $subject->export());
    }
}
