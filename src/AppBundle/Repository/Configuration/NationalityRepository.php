<?php

namespace AppBundle\Repository\Configuration;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class NationalityRepository extends EntityRepository
{

    
    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllNationalities($options=[])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('n.nationality_id,n.nationality_name')
            ->from('cfg_nationalities', 'n');

        $queryBuilder = $this->setFilterOptions($options,$queryBuilder);
        $queryBuilder = $this->setSortOptions($options,$queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if(!empty($options['nationalityName']))
        {
            $queryBuilder->Where('lower(n.nationality_name) LIKE lower(:nationalityName)')
                ->setParameter('nationalityName','%'.$options['nationalityName'].'%');
        }



        return $queryBuilder;
    }
    
    
    
    
    

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {
        $options['sortType'] == 'desc' ? $sortType='desc': $sortType='asc';

        if($options['sortBy'] === 'nationalityName')
        {
             $queryBuilder->addOrderBy('nationality_name',$sortType);
        }

        else{
             $queryBuilder->addOrderBy('nationality_id','DESC');
        }

        return $queryBuilder;
    }

    public function countAllNationalities(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT n.nationality_id) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }






    
}
