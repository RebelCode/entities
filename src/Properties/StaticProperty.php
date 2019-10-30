<?php

namespace RebelCode\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * A property implementation that always returns the same non-changing value.
 *
 * @since [*next-version*]
 */
class StaticProperty implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param mixed $value The value for this property.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getValue(EntityInterface $entity)
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return [];
    }
}
