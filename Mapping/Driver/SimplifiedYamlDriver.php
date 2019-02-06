<?php

namespace Pj\EntityExtendBundle\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver as DoctrineSimplifiedYamlDriver;
use Pj\EntityExtendBundle\Mapping\Driver\Traits\ExtendedEntitiesTrait;

/**
 * Class SimplifiedYamlDriver.
 *
 * NOTE: If used along with Gedmo Doctrine Extensions, class name has to be `Yaml`, otherwise
 * Gedmo's `ExtensionMetadataFactory` will fall back to Annotation driver. Brilliant!
 *
 * Check out the Gedmo driver loader here: https://github.com/Atlantic18/DoctrineExtensions/blob/v2.4.x/lib/Gedmo/Mapping/ExtensionMetadataFactory.php#L145-L159
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 */
class SimplifiedYamlDriver extends DoctrineSimplifiedYamlDriver
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
        if (true === $isTransient) {
            return true;
        }

        return $this->isExtendedEntity($className);
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

    /**
     * Merges mappings recursively and overrides duplicated values with second mappings values.
     *
     * @param array $mapping1
     * @param array $mapping2
     *
     * @return array
     */
    protected function mergeMappings(array &$mapping1, array &$mapping2): array
    {
        $merged = $mapping1;
        foreach ($mapping2 as $key => &$value) {
            if (\is_array($value) && isset($merged[$key]) && \is_array($merged[$key])) {
                $merged[$key] = $this->mergeMappings($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
