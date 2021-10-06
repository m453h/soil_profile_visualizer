<?php

namespace AppBundle\Repository\Data;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class CropsInRegionRepository extends EntityRepository
{

    
    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllCropsInRegion($options=[])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('record_id,crop_category,crop_name,harvested_area,production_value,record_year')
            ->from('tbl_crops_in_region', 'c');

        $queryBuilder = $this->setFilterOptions($options,$queryBuilder);
        $queryBuilder = $this->setSortOptions($options,$queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if(!empty($options['regionCode']))
        {
            $queryBuilder->Where('region_code=:regionCode')
                ->setParameter('regionCode',$options['regionCode']);
        }

        return $queryBuilder;
    }
    

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {
        $options['sortType'] == 'desc' ? $sortType='desc': $sortType='asc';

        if($options['sortBy'] === 'name')
        {
             $queryBuilder->addOrderBy('crop_name',$sortType);
        }
        else
        {
             $queryBuilder->addOrderBy('record_id','DESC');
        }

        return $queryBuilder;
    }

    public function countAllCropsInRegion(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT record_id) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    /**
     * @param $regionCode
     * @param $row
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function recordCropsInRegion($regionCode,$row)
    {
        set_time_limit(0);

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('tbl_crops_in_region')
            ->setValue('region_code', ':region_code')
            ->setValue('crop_category', ':crop_category')
            ->setValue('crop_code', ':crop_code')
            ->setValue('crop_name', ':crop_name')
            ->setValue('harvested_area', ':harvested_area')
            ->setValue('production_value', ':production_value')
            ->setValue('record_year', ':record_year')

            ->setParameter('region_code', $regionCode)
            ->setParameter('crop_category', $row['crop_parent'])
            ->setParameter('crop_code', $row['crop'])
            ->setParameter('crop_name', $row['crop_name'])
            ->setParameter('harvested_area', $row['harvested_area'])
            ->setParameter('production_value', $row['value_of_production'])
            ->setParameter('record_year', $row['year'])
            ->execute();

    }

    /**
     * @param $regionCode
     * @return string
     */
    public function getTopCropsInRegion($regionCode)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('crop_name')
            ->from('tbl_crops_in_region', 'c')
            ->andWhere('region_code=:regionCode')
            ->addOrderBy('harvested_area','DESC')
            ->setMaxResults(10)
            ->setParameter('regionCode',$regionCode)
            ->execute()
            ->fetchAll();

        $data = [];
        foreach ($results as $crop)
        {
          array_push($data,$crop['crop_name']);
        }

        return implode($data,', ');
    }


}
