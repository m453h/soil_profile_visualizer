<?php

namespace AppBundle\Doctrine;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use PDO;

class CustomHydrator extends AbstractHydrator
{

    /**
     * Hydrates all rows from the current statement instance at once.
     *
     * @return array
     */
    protected function hydrateAllData()
    {
        // TODO: Implement hydrateAllData() method.
        return $this->_stmt->fetchAll(PDO::FETCH_NUM);
    }
}