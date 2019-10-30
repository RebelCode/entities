<?php

namespace RebelCode\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * Implementation of a property that uses callbacks for reading and writing values.
 *
 * @since [*next-version*]
 */
class CallbackProperty implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var callable|null
     */
    protected $getter;

    /**
     * @since [*next-version*]
     *
     * @var callable|null
     */
    protected $setter;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param callable|null $getter The getter callback. Receives the entity instance as arguments and should return
     *                              the property value.
     * @param callable|null $setter The setter callback. Receives the entity instance and value as arguments and should
     *                              return a commit as an associative array that maps store keys to their new value.
     */
    public function __construct(callable $getter = null, callable $setter = null)
    {
        $this->getter = $getter;
        $this->setter = $setter;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getValue(EntityInterface $entity)
    {
        if (!is_callable($this->getter)) {
            return null;
        }

        return call_user_func_array($this->getter, [$entity]);
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        if (!is_callable($this->setter)) {
            return [];
        }

        return call_user_func_array($this->setter, [$entity, $value]);
    }
}
