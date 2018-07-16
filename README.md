# EntityExtendBundle

The EntityExtendBundle is a Symfony3 bundle, which lets extend doctrine ORM entities. Doctrine MappedSuperclass is useful when you want to declare common information for other entities, but every new it's subclass must be new entity. Sometimes you need to extend existing entity, without introducing new one. EntityExtendBundle can help you.

# Installation
## Composer
``` composer require evozon-php/entity-extend-bundle ```
## Enable the bundle
```
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new \Pj\EntityExtendBundle\EntityExtendBundle(),
        // ...
    );
}
```

# Usage
EntityExtendBundle can be used with annotations or yml mapping. Lets say you have Product entity:
```
<?php

namespace Acme\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BaseProduct.
 *
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class BaseProduct
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * Getter for name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
}
```
and you want to add extra field (lets say description) to it without changing BaseProduct class. This is simple from OOP point of view:
```
<?php

namespace Custom\ProductBundle\Entity;

use Acme\ProductBundle\Entity\Product as BaseProduct;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CustomProduct.
 *
 * @ORM\Entity
 */
class CustomProduct extends BaseProduct
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;
    
    /**
     * Getter for description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Setter for description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }
}
```
but now doctrine will think that CustomProduct is new entity with it's own DB table and this is problem, because we want that CustomProduct would be same product entity and would use same DB table. With EntityExtendBundle you can solve this problem. First of all you need to add extended entities list to your config.yml:
```
entity_extend:
    extended_entities:
        Acme\ProductBundle\Entity\BaseProduct: Custom\ProductBundle\Entity\CustomProduct
```
Second step is to say that CustomProduct extends BaseProduct mapping information.

## Annotations mapping
If you are using annotations mapping, you need to add @ExtentedEntity annotation to CustomProduct class:
```
/**
 * Class CustomProduct.
 *
 * @ORM\Entity
 * @Pj\ExtendedEntity(className="Acme\ProductBundle\Entity\BaseProduct")
 */
class Product extends BaseProduct
{
    //...
```

## yml mapping
If you are using yml mapping, you need to add extended_entity property to CustomProduct.yml:
```
Custom\ProductBundle\Entity\CustomProduct:
    extended_entity: Acme\ProductBundle\Entity\BaseProduct
    type: entity
    fields:
        description:
            type: text
            nullable: true
```

That's it. Now doctrine will know, that CustomProduct is mapped with same products DB table. If you are using doctrine migrations, generated migration will add descrition field to products table instead of creating new DB table.

Of course EntityExtendBundle can be used not only for adding new fields, but for overriding already defined as well, for example increasing name field's length.
