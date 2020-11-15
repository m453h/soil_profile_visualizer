<?php

namespace AppBundle\Repository\Configuration;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class CountryRepository extends EntityRepository
{

    
    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllCountries($options=[])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('c.country_id,c.country_name')
            ->from('cfg_countries', 'c');

        $queryBuilder = $this->setFilterOptions($options,$queryBuilder);
        $queryBuilder = $this->setSortOptions($options,$queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if(!empty($options['countryName']))
        {
            $queryBuilder->Where('lower(c.country_name) LIKE lower(:countryName)')
                ->setParameter('countryName','%'.$options['countryName'].'%');
        }



        return $queryBuilder;
    }
    

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {
        $options['sortType'] == 'desc' ? $sortType='desc': $sortType='asc';

        if($options['sortBy'] === 'countryName')
        {
             $queryBuilder->addOrderBy('country_name',$sortType);
        }

        else{
             $queryBuilder->addOrderBy('country_id','ASC');
        }

        return $queryBuilder;
    }

    public function countAllCountries(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT c.country_id) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    
}
