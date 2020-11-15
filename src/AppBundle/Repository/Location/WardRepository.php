<?php

namespace AppBundle\Repository\Location;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class WardRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllWards($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('w.ward_code AS "gid",ward_code,ward_name,district_name,region_name')
            ->from('spd_tanzania_wards', 'w')
            ->leftJoin('w','spd_tanzania_districts','d','d.district_code=w.district_code')
            ->leftJoin('d','spd_tanzania_regions','r','r.region_code=d.region_code');

        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['wardName']))
        {
            return $queryBuilder->andWhere('lower(w.ward_name) LIKE lower(:name)')
                ->setParameter('name', '%' . $options['name'] . '%');
        }

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

        if ($options['sortBy'] === 'wardName')
        {
            return $queryBuilder->addOrderBy('ward_name', $sortType);
        }

        if ($options['sortBy'] === 'regionName')
        {
            return $queryBuilder->addOrderBy('region_name', $sortType);
        }

        if ($options['sortBy'] === 'districtName')
        {
            return $queryBuilder->addOrderBy('district_name', $sortType);
        }


        return $queryBuilder->addOrderBy('w.ward_code', 'desc');

    }


    public function countAllWards(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT w.ward_code) AS total_results')
                ->groupBy('w.gid')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }

    public function getWardGeometry($options = [])
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('ward_name,
                               ward_code,
                               \'ward\' AS level,
                               ST_AsGeoJSON(ST_Transform(ward_geometry,4326))
                               ')
            ->from('spd_tanzania_wards', 'w')
            ->join('w','spd_tanzania_districts','d','w.district_code=d.district_code');


        if(!empty($options['districtCode']))
        {
            $queryBuilder->andWhere('w.district_code=:districtCode')
                ->setParameter('districtCode',$options['districtCode']);
        }

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }

    public function findWardsByDistrict($districtCode)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('ward_code as value,ward_name AS "name"')
            ->from('spd_tanzania_wards', 'w')
            ->where('w.district_code=:districtCode')
            ->setParameter('districtCode',$districtCode);

        return $queryBuilder->execute()->fetchAll();
    }

    public function getWardSpatialStatistics($options)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select("region_name,
                               ward_name,
                               w.ward_code,
                               'ward' AS level,
                               w.ward_code AS results,
                               SUM (CASE WHEN status = '1' THEN 1 ELSE 0 END) AS \"Active\",
                               SUM (CASE WHEN status = '2' THEN 1 ELSE 0 END) AS \"Recovered\",
                               SUM (CASE WHEN status = '3' THEN 1 ELSE 0 END) AS \"Fatal\", 
                               ST_AsGeoJSON(ST_Transform(ward_geometry,4326))
                               ")
            ->from('tbl_cases', 'c')
            ->rightJoin('c','spd_tanzania_regions','r','c.region_code=r.region_code')
            ->join('r','spd_tanzania_districts','d','d.region_code=r.region_code')
            ->join('d','spd_tanzania_wards','w','w.district_code=d.district_code')
            ->where('w.district_code=:districtCode')
            ->setParameter('districtCode',$options['districtCode'])
            ->addGroupBy('w.ward_code')
            ->addGroupBy('r.region_code');


        return $queryBuilder
            ->execute()
            ->fetchAll();
    }
}
