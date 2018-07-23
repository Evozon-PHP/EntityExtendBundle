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
     * Returns whether the class with the specified name is transient. Only non-transient
     * classes, that is entities and mapped superclasses, should have their metadata loaded.
     *
     * A class is non-transient if it is annotated with an annotation
     * from the {@see AnnotationDriver::entityAnnotationClasses}.
     *
     * @param string $className
     *
     * @return boolean
     */
    public function isTransient($className)
    {
        $isTransient = parent::isTransient($className);

        if (!$isTransient
            && isset($this->extendedEntities[$className])
            && !\in_array($className, $this->nonTransient, true))
        {
            $isTransient = true;
        }

        return $isTransient;
    }

    /**
     * Gets the element of schema meta data for the class from the mapping file.
     * This will lazily load the mapping file if it is not loaded yet.
     *
     * Overridden in order to merger mapping with parent class if 'extended_entity' is provided.
     *
     * @param string $className
     *
     * @return array The element of schema meta data.
     */
    public function getElement($className)
    {
        $result = parent::getElement($className);
        if (isset($result['extended_entity'])) {
            $extendedElement = $this->getElement($result['extended_entity']);
            unset($result['extended_entity']);

            $result = $this->mergeMappings($extendedElement, $result);
        }

        return $result;
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
