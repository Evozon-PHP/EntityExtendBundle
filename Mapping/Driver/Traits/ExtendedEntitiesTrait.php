<?php

namespace Pj\EntityExtendBundle\Mapping\Driver\Traits;

/**
 * Trait ExtendedEntitiesTrait.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 * @author Balazs Csaba <csaba.balazs@evozon.com>
 */
trait ExtendedEntitiesTrait
{
    /**
     * @var array
     */
    private $extendedEntities = [];

    /**
     * @var array
     */
    private $nonTransient = [];

    /**
     * Setter for extendedEntities.
     *
     * @param array $extendedEntities
     *
     * @return $this
     */
    public function setExtendedEntities(array $extendedEntities): self
    {
        $this->extendedEntities = $extendedEntities;

        return $this;
    }

    /**
     * Setter for non-transient entities.
     *
     * @param array $nonTransient
     *
     * @return $this
     */
    public function setNonTransient(array $nonTransient): self
    {
        $this->nonTransient = $nonTransient;

        return $this;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    protected function isExtendedEntity(string $className): bool
    {
        return isset($this->extendedEntities[$className])
            && !\in_array($className, $this->nonTransient, true);
    }

    /**
     * @param string $className
     *
     * @return string
     */
    protected function getFinalClass(string $className): string
    {
        if (!$this->isExtendedEntity($className)) {
            return $className;
        }

        return $this->extendedEntities[$className];
    }
}
