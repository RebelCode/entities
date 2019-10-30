<?php

namespace RebelCode\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * Abstract implementation of a property that decorates another property.
 *
 * @since [*next-version*]
 */
abstract class AbstractDecoratorProperty implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var PropertyInterface
     */
    protected $property;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PropertyInterface $property The property instance to decorate.
     */
    public function __construct(PropertyInterface $property)
    {
        $this->property = $property;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getValue(EntityInterface $entity)
    {
        return $this->getter($entity, $this->property->getValue($entity));
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return $this->property->setValue($entity, $this->setter($entity, $value));
    }

    /**
     * Retrieves the actual value for the value returned by the original property.
     *
     * @since [*next-version*]
     *
     * @param EntityInterface $entity The entity instance.
     * @param mixed           $value  The value returned by the original property.
     *
     * @return mixed The value.
     */
    abstract protected function getter(EntityInterface $entity, $value);

    /**
     * Retrieves the actual value to set to the original property.
     *
     * @since [*next-version*]
     *
     * @param EntityInterface $entity The entity instance.
     * @param mixed           $value  The value being set.
     *
     * @return mixed The value.
     */
    abstract protected function setter(EntityInterface $entity, $value);
}
