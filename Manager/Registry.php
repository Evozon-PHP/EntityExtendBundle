<?php

namespace Pj\EntityExtendBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry as DoctrineRegistry;
use Pj\EntityExtendBundle\Mapping\Driver\Traits\ExtendedEntitiesTrait;

/**
 * Class Registry.
 *
 * @copyright Evozon Systems SRL (http://www.evozon.com/)
 * @author    Csaba Balazs <csaba.balazs@evozon.com>
 */
class Registry extends DoctrineRegistry
{
    use ExtendedEntitiesTrait;

    /**
     * {@inheritdoc}
     */
    public function getManagerForClass($class)
    {
        $className = $this->getFinalClass($class);

        return parent::getManagerForClass($className);
    }
}
