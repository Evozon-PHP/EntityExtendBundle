<?php

namespace Pj\EntityExtendBundle\Mapping\Driver;

use Doctrine\Bundle\DoctrineBundle\Registry as DoctrineRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver as DoctrineAnnotationDriver;
use Pj\EntityExtendBundle\Mapping\Driver\Traits\ExtendedEntitiesTrait;
use Pj\EntityExtendBundle\Mapping\ExtendedEntity;

/**
 * Class AnnotationDriver.
 *
 * @author Paulius Jarmalavičius <paulius.jarmalavicius@gmail.com>
 */
class AnnotationDriver extends DoctrineAnnotationDriver
{
    use ExtendedEntitiesTrait;

    /**
     * @var DoctrineRegistry
     */
    protected $registry;

    /**
     * @param DoctrineRegistry $registry
     *
     * @return $this
     */
    public function setRegistry(DoctrineRegistry $registry): self
    {
        $this->registry = $registry;

        return $this;
    }

    /**
     * Rewrite association target entities to point to extended entity classes.
     *
     * {@inheritDoc}
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        /* @var $metadata \Doctrine\ORM\Mapping\ClassMetadataInfo */
        parent::loadMetadataForClass($className, $metadata);

        $classAnnotations = $this->getClassAnnotations($metadata);
        if (isset($classAnnotations['Pj\EntityExtendBundle\Mapping\ExtendedEntity'])) {
            /** @var ExtendedEntity $annotation */
            $annotation = $classAnnotations['Pj\EntityExtendBundle\Mapping\ExtendedEntity'];
            $extendedEntityClass = $annotation->className;
            $cmf = $this->getManager()->getMetadataFactory();
            /** @var \Doctrine\ORM\Mapping\ClassMetadataInfo $extendedEntityMetadata */
            $extendedEntityMetadata = $cmf->getMetadataFor($extendedEntityClass);

            // Set by parent entity.
            $metadata->setPrimaryTable($extendedEntityMetadata->table);
        }

        // Rewrite targetEntity to point to extended entity class
        foreach ($metadata->getAssociationMappings() as $assocName => $assocMapping) {
            if (isset($this->extendedEntities[$assocMapping['targetEntity']])) {
                $assocMapping['targetEntity'] = $this->extendedEntities[$assocMapping['targetEntity']];
            }
            $metadata->associationMappings[$assocName] = $assocMapping;
        }
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
     * Returns class annotations.
     *
     * @param ClassMetadataInfo $metadata
     *
     * @return array
     */
    protected function getClassAnnotations($metadata)
    {
        $class = $metadata->getReflectionClass();
        if ( !$class) {
            // this happens when running annotation driver in combination with
            // static reflection services. This is not the nicest fix
            $class = new \ReflectionClass($metadata->name);
        }

        return $this->readAnnotations($class);
    }

    /**
     * Reads class annotations.
     *
     * @param \ReflectionClass $class
     * @return array
     */
    protected function readAnnotations(\ReflectionClass $class)
    {
        $classAnnotations = $this->reader->getClassAnnotations($class);
        if ($classAnnotations) {
            foreach ($classAnnotations as $key => $annotations) {
                if (!is_numeric($key)) {
                    continue;
                }

                $classAnnotations[get_class($annotations)] = $annotations;
            }
        }

        return $classAnnotations;
    }

    /**
     * @return ObjectManager
     */
    private function getManager(): ObjectManager
    {
        return $this->registry->getManager();
    }
}
