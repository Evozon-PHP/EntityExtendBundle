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
}