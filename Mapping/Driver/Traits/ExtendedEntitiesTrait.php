<?php

namespace Pj\EntityExtendBundle\Mapping\Driver\Traits;

/**
 * Trait ExtendedEntitiesTrait.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 */
trait ExtendedEntitiesTrait
{
    /**
     * @var array
     */
    private $extendedEntities = [];

    /**
     * Setter for extendedEntities.
     *
     * @param array $extendedEntities
     *
     * @return $this
     */
    public function setExtendedEntities(array $extendedEntities)
    {
        $this->extendedEntities = $extendedEntities;

        return $this;
    }
}