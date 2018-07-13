<?php

namespace Pj\EntityExtendBundle\Mapping\Driver;

use Doctrine\ORM\Mapping\Driver\SimplifiedYamlDriver as DoctrineSimplifiedYamlDriver;
use Pj\EntityExtendBundle\Mapping\Driver\Traits\ExtendedEntitiesTrait;

/**
 * Class SimplifiedYmlDriver.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 */
class SimplifiedYmlDriver extends DoctrineSimplifiedYamlDriver
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

        if (!$isTransient && isset($this->extendedEntities[$className])) {
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
     * Merges mappings recursively and overrides duplicated values with second mappings values.
     *
     * @param array $mapping1
     * @param array $mapping2
     *
     * @return array
     */
    protected function mergeMappings(array &$mapping1, array &$mapping2)
    {
        $merged = $mapping1;
        foreach ($mapping2 as $key => &$value) {
            if (is_array ($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeMappings($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}