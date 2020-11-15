<?php

namespace AppBundle\Repository\Location;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class DistrictRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllDistricts($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('d.district_code AS "gid",district_code,district_name,region_name')
            ->from('spd_tanzania_districts', 'd')
            ->join('d','spd_tanzania_regions','r','r.region_code=d.region_code');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['districtName']))
        {
            return $queryBuilder->andWhere('lower(d.district_name) LIKE lower(:districtName)')
                ->setParameter('districtName', '%' . $options['districtName'] . '%');
        }

        if (!empty($options['regionName']))
        {
            return $queryBuilder->andWhere('lower(r.region_name) LIKE lower(:regionName)')
                ->setParameter('regionName', '%' . $options['regionName'] . '%');
        }


        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

        if ($options['sortBy'] === 'districtName')
        {
            return $queryBuilder->addOrderBy('district_name', $sortType);
        }

        if ($options['sortBy'] === 'regionName')
        {
            return $queryBuilder->addOrderBy('region_name', $sortType);
        }

        if ($options['sortBy'] === 'code')
        {
            return $queryBuilder->addOrderBy('district_code', $sortType);
        }
        

        return $queryBuilder->addOrderBy('d.district_code', 'desc');

    }

    public function countAllDistricts(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT d.district_code) AS total_results')
                ->groupBy('d.gid')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }

    public function getDistrictGeometry($options = [])
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('district_name,
                               district_code,
                               \'district\' AS level,
                               ST_AsGeoJSON(ST_Transform(district_geometry,4326))
                               ')
            ->from('spd_tanzania_districts', 'd')
            ->join('d','spd_tanzania_regions','r','d.region_code=r.region_code');


        if(!empty($options['regionCode']))
        {
            $queryBuilder->andWhere('d.region_code=:regionCode')
                ->setParameter('regionCode',$options['regionCode']);
        }

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }


    public function findDistrictsByRegion($regionCode)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('district_code as value,district_name AS "name"')
            ->from('spd_tanzania_districts', 'd')
            ->where('d.region_code=:regionCode')
            ->setParameter('regionCode',$regionCode);

        return $queryBuilder->execute()->fetchAll();
    }


    public function getDistrictSpatialStatistics($options)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select("
                               region_name,
                               district_name,
                               d.district_code,
                               'district' AS level,
                               d.district_code AS results,
                               SUM (CASE WHEN status = '1' THEN 1 ELSE 0 END) AS \"Active\",
                               SUM (CASE WHEN status = '2' THEN 1 ELSE 0 END) AS \"Recovered\",
                               SUM (CASE WHEN status = '3' THEN 1 ELSE 0 END) AS \"Fatal\", 
                               ST_AsGeoJSON(ST_Transform(district_geometry,4326))
                               ")
            ->from('tbl_cases', 'c')
            ->rightJoin('c','spd_tanzania_regions','r','c.region_code=r.region_code')
            ->join('r','spd_tanzania_districts','d','d.region_code=r.region_code')
            ->where('r.region_code=:regionCode')
            ->setParameter('regionCode',$options['regionCode'])
            ->addGroupBy('d.district_code')
            ->addGroupBy('r.region_code');

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }


}
