<?php

namespace AppBundle\Repository\Configuration;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class SoilTypeRepository extends EntityRepository
{


    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllSoilType($options=[])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('code AS id,code,name,map_color')
            ->from('cfg_soil_types', 't');

        $queryBuilder = $this->setFilterOptions($options,$queryBuilder);
        $queryBuilder = $this->setSortOptions($options,$queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if(!empty($options['name']))
        {
            $queryBuilder->Where('lower(t.name) LIKE lower(:name)')
                ->setParameter('name','%'.$options['name'].'%');
        }
        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {
        $options['sortType'] == 'desc' ? $sortType='desc': $sortType='asc';

        if($options['sortBy'] === 'name')
        {
            $queryBuilder->addOrderBy('name',$sortType);
        }

        else{
            $queryBuilder->addOrderBy('code','ASC');
        }

        return $queryBuilder;
    }

    public function countAllSoilTypes(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT t.code) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    public function getSoilProfileGeometry($options = [])
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('soil_type,
                               main_type,
                               map_color,
                               \'region\' AS level,
                               ST_AsGeoJSON(ST_Transform(geom,4326))
                               ')
            ->from('spd_tanzania_soil_profile', 'p')
            ->join('p','cfg_soil_types','t','t.code=p.soil_type');

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }


    public function getRegionSoilProfileGeometry()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT soil_type,main_type,map_color,'region' AS level,
                     ST_AsGeoJSON(ST_Transform(geom,3795)) 
                     FROM spd_tanzania_soil_profile p, cfg_soil_types t
                     WHERE  t.code=p.soil_type";


        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function reverseGeocode($latitude,$longitude)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('region_name AS region','district_name AS district','ward_name AS ward','recommended_crops')
            ->from('spd_tanzania_regions', 'r')
            ->join('r','spd_tanzania_districts','d','d.region_code=r.region_code')
            ->join('d','spd_tanzania_wards','w','w.district_code=d.district_code')
            ->andWhere('ST_Within(ST_SetSRID(ST_MakePoint(:longitude,:latitude),3795),ward_geometry)')
            ->setParameter('longitude',$longitude)
            ->setParameter('latitude',$latitude);

        return $queryBuilder
            ->execute()
            ->fetch();
    }

    public function reverseGeocodeSoilProperty($latitude,$longitude)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('soil_type','main_type','potential_use','limitations')
            ->from('spd_tanzania_soil_profile', 'r')
            ->leftJoin('r','cfg_soil_types','slt','slt.code=r.soil_type')
            ->andWhere('ST_Within(ST_SetSRID(ST_MakePoint(:longitude,:latitude),3795),geom)')
            ->setParameter('longitude',$longitude)
            ->setParameter('latitude',$latitude);

        return $queryBuilder
            ->execute()
            ->fetch();
    }

    public function setSpatialFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if(!empty($options['regionCode']))
        {
            $queryBuilder
                ->join()
                ->Where('lower(t.name) LIKE lower(:name)')
                ->setParameter('name','%'.$options['name'].'%');
        }
        return $queryBuilder;
    }



    public function getData()
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('main_type,soil_type')

            ->from('spd_tanzania_soil_profile', 'p')
            ->addOrderBy('main_type')
            ->addGroupBy('main_type')
            ->addGroupBy('soil_type');


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
