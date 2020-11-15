<?php

namespace AppBundle\Repository\Accounts;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class AuditTrailRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllAuditTrailLogs($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('
        log_id,
        time_logged,
        ip_address,
        action,
        entity_descriptor,
        original_data,
        final_data
        '
        )

            ->from('log_audit_trail', 'l');
        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function countAllAuditTrailLogs(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT log_id) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {

        if (!empty($options['userId']))
        {
            return $queryBuilder->andwhere('user_id=:Id')
                ->setParameter('Id', $options['userId']);
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';


        return $queryBuilder->addOrderBy('log_id', 'desc');

    }

    

}
