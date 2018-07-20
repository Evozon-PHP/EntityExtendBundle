<?php

namespace Pj\EntityExtendBundle\Mapping\Driver;

use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver as DoctrineSimplifiedXmlDriver;
use Pj\EntityExtendBundle\Mapping\Driver\Traits\ExtendedEntitiesTrait;

/**
 * Class SimplifiedXmlDriver.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 */
class SimplifiedXmlDriver extends DoctrineSimplifiedXmlDriver
{
    use ExtendedEntitiesTrait;

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames()
    {
        $classNames = parent::getAllClassNames();

        return array_filter(
            $classNames,
            function ($className) {
                return !isset($this->extendedEntities[$className]);
            }
        );
    }

    /**
     * Rewrite association target entities to point to extended entity classes.
     *
     * @param               $className
     * @param ClassMetadata $metadata
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        /* @var $metadata \Doctrine\ORM\Mapping\ClassMetadataInfo */
        parent::loadMetadataForClass($className, $metadata);

        // Rewrite targetEntity to point to extended entity class
        foreach ($metadata->getAssociationMappings() as $assocName => $assocMapping) {
            if (isset($this->extendedEntities[$assocMapping['targetEntity']])) {
                $assocMapping['targetEntity'] = $this->extendedEntities[$assocMapping['targetEntity']];
            }
            $metadata->associationMappings[$assocName] = $assocMapping;
        }
    }
}
