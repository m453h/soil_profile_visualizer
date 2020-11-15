<?php

namespace AppBundle\Repository\Location;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class RegionRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllRegions($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('r.region_code AS "gid",region_code,region_name')
            ->from('spd_tanzania_regions', 'r');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['name']))
        {
            return $queryBuilder->andWhere('lower(r.region_name) LIKE lower(:name)')
                ->setParameter('name', '%' . $options['name'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

        if ($options['sortBy'] === 'name')
        {
            return $queryBuilder->addOrderBy('region_name', $sortType);
        }

        if ($options['sortBy'] === 'code')
        {
            return $queryBuilder->addOrderBy('region_code', $sortType);
        }

        return $queryBuilder->addOrderBy('r.region_code', 'desc');

    }


    public function countAllRegions(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT r.region_code) AS total_results')
                ->groupBy('r.gid')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }





    public function getRegionGeometry($options = [])
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        
        $queryBuilder->select('region_name,
                               region_code,
                               \'region\' AS level,
                               region_code AS results,
                               ST_AsGeoJSON(ST_Transform(region_geometry,4326))
                               ')
            ->from('spd_tanzania_regions', 'p');
        
        return $queryBuilder
            ->execute()
            ->fetchAll();
    }


    public function getRegionSpatialStatistics($options = [])
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select("region_name,
                               r.region_code,
                               'region' AS level,
                               r.region_code AS results,
                               SUM (CASE WHEN status = '1' THEN 1 ELSE 0 END) AS \"Active\",
                               SUM (CASE WHEN status = '2' THEN 1 ELSE 0 END) AS \"Recovered\",
                               SUM (CASE WHEN status = '3' THEN 1 ELSE 0 END) AS \"Fatal\", 
                               ST_AsGeoJSON(ST_Transform(region_geometry,4326))
                               ")
            ->from('tbl_cases', 'c')
            ->rightJoin('c','spd_tanzania_regions','r','c.region_code=r.region_code')
            ->addGroupBy('r.region_code');



        return $queryBuilder
            ->execute()
            ->fetchAll();
    }

}
