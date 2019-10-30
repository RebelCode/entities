<?php

namespace RebelCode\Entities\UnitTests\Stores;

use PHPUnit\Framework\TestCase;
use RebelCode\Entities\Stores\ArrayStore;

/**
 * @since [*next-version*]
 */
class ArrayStoreTest extends TestCase
{
    /**
     * @since [*next-version*]
     */
    public function testConstruct()
    {
        $subject = new ArrayStore([]);

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
            $array = [
                $key => $val,
            ];
        }
        {
            $subject = new ArrayStore($array);
        }

        static::assertSame($val, $subject->get($key));
    }

    /**
     * @since [*next-version*]
     */
    public function testGetMissing()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');
            $array = [
                $key => $val,
            ];
        }
        {
            $key2 = uniqid('missing');
            $subject = new ArrayStore($array);
        }

        static::setExpectedException('OutOfBoundsException');

        static::assertSame($val, $subject->get($key2));
    }

    /**
     * @since [*next-version*]
     */
    public function testHas()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');
            $array = [
                $key => $val,
            ];
        }
        {
            $subject = new ArrayStore($array);
        }

        static::assertTrue($subject->has($key));
    }

    /**
     * @since [*next-version*]
     */
    public function testHasMissing()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');
            $array = [
                $key => $val,
            ];
        }
        {
            $key2 = uniqid('missing');
            $subject = new ArrayStore($array);
        }

        static::assertFalse($subject->has($key2));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetNewKey()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');
            $key2 = uniqid('key2');
            $val2 = uniqid('val2');
            $array = [
                $key => $val,
            ];
        }
        {
            $subject = new ArrayStore($array);
        }

        $store = $subject->set([$key2 => $val2]);

        static::assertSame($val2, $store->get($key2));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetOverwrite()
    {
        {
            $key = uniqid('key');
            $val = uniqid('val');
            $val2 = uniqid('val2');
            $array = [
                $key => $val,
            ];
        }
        {
            $subject = new ArrayStore($array);
        }

        $store = $subject->set([$key => $val2]);

        static::assertSame($val2, $store->get($key));
    }

    /**
     * @since [*next-version*]
     */
    public function testSetMultiple()
    {
        {
            $key1 = uniqid('key1');
            $val1 = uniqid('val1');

            $key2 = uniqid('key2');
            $val2 = uniqid('val2');
            $new2 = uniqid('new2');

            $key3 = uniqid('key3');
            $val3 = uniqid('val3');
            $new3 = uniqid('new3');

            $key4 = uniqid('key4');
            $val4 = uniqid('val4');

            $array = [
                $key1 => $val1,
                $key2 => $val2,
                $key3 => $val3
            ];
        }
        {
            $subject = new ArrayStore($array);
        }

        $store = $subject->set([
            $key2 => $new2,
            $key3 => $new3,
            $key4 => $val4
        ]);

        static::assertSame($val1, $store->get($key1));
        static::assertSame($new2, $store->get($key2));
        static::assertSame($new3, $store->get($key3));
        static::assertSame($val4, $store->get($key4));
    }
}
