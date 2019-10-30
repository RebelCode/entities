# Entities

A lightweight library for working with class-less entities, built for extendability.

This package aims to provide a robust solution for flexible entities, for when associative arrays are not enough. In
this context an entity is an object that is not represented by its own class or interface.

Our primary use case for using entities was to allow 1st or 3rd party code to extend the data and behavior our objects
without having to introduce extension classes and interfaces, which would bring with them other complications. On the
other hand, using a hash-map-like data structure instead of formal objects means that our objects have no contract to
obey, be it an interface or otherwise.

## Design

Entities are hash-map structures, meaning that the data is read from entities using keys.

At its core, the system operates using **stores**, which are a simple abstraction for the storage system such as a
database, filesystem, memory, remote API, etc.

```php
interface StoreInterface
{
    public function get($key) : mixed;
    public function has($key) : bool;
    public function set(array $data) : StoreInterface;
}
```

The store is read using **properties**, which represent the keys of an entity. Properties are agnostic of any entity
instance; instead they receive the entity instance during read and write calls. It should be noted that properties do
not perform any actual writing, but instead return a "partial commit" as an associative array.

```php
interface PropertyInterface
{
    public function getValue($entity) : mixed;
    public function setValue($entity, $value) : array;
}
```

A **schema** is a data-providing object that exposes a map of properties and their corresponding default values. Within
this system, schemas represent the "contract". When an entity uses a schema, it MUST provide access to all of the
properties given by the schema and use the default values wherever a failure may occur.

```php
interface SchemaInterface
{
    public function getProperties() : array;
    public function getDefaults() : array;
}
```

And finally, **entities**. Entities expose an API similar to that of stores, but with some additions.

```php
interface EntityInterface
{
    public function get($key) : mixed;
    public function set(array $data) : EntityInterface;
    public function getStore() : StoreInterface;
    public function getSchema() : SchemaInterface;
    public function export() : array;
}
```


First, the store is exposed to allow consumer code to access the raw data (ex. properties often require the store for reading).

The schema is also made available to allow consumer code to be aware of what keys may be accessed and even perform type
checking. The `export()` method allows the entity to be reduced to an array for consumer code that might need to dump the
entire entity (ex. REST API endpoints), without introducing iterator mechanics.

The result is a system that separates concerns quite nicely.
* Properties are reusable read-write objects that can transform raw data into the expected form and vice-versa.
* Schemas group properties together to represent an entity type, agnostic of the store.
* Stores provide a simple abstraction API for any form of storage.
* Entities are agnostic of their schema or store implementations.

With this design, schemas replace the traditional class and interface combo for object contracts. Because they have no
awareness of the entity's store, the same schema can be used in conjunction with any kind of storage system; database,
remote web API, file system or in-memory array.

## Example Usage

```php
class UserSchema implements SchemaInterface {
    public function getProperties() {
        return [
            'id' => new SimpleProperty('user_id'),
            'username' => new SimpleProperty('user_name'),
            'fullname' => new ConcatProperty(['first_name', 'last_name']),
        ];
    }

    public function getDefaults() {
        return [
            'id' => 0,
            'username' => '',
            'fullname' => '',
        ];
    }
}

$schema = new UserSchema();

// FROM A DATABASE
$store = $someDb->getUser(123);
$entity = new Entity($schema, $store);

// FROM AN ARRAY
$store = new ArrayStore([
    'user_id' => 123,
    'user_name' => 'foo',
    'first_name' => 'Foo',
    'last_name' => 'Bar',
]);
$entity = new Entity($schema, $store);
```
