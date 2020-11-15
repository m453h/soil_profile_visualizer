<?php

namespace AppBundle\Repository\Data;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class CaseDetailRepository extends EntityRepository
{

    
    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllCaseDetails($options=[])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('c.case_id,region_name,district_name,ward_name,age,status')
            ->from('tbl_cases', 'c')
            ->join('c','spd_covid_regions','r','r.region_code=c.region_code')
            ->leftJoin('c','spd_covid_districts','d','d.district_code=c.district_code')
            ->leftJoin('c','spd_covid_wards','w','w.ward_code=c.ward_code');

        $queryBuilder = $this->setFilterOptions($options,$queryBuilder);
        $queryBuilder = $this->setSortOptions($options,$queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {


        if (!empty($options['caseFolderId']))
        {
            return $queryBuilder->andWhere('c.case_folder_id = :caseFolderId')
                ->setParameter('caseFolderId',$options['caseFolderId']);
        }

        return $queryBuilder;
    }
    

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {
        $options['sortType'] == 'desc' ? $sortType='desc': $sortType='asc';

        if($options['sortBy'] === 'folderOpenDate')
        {
             $queryBuilder->addOrderBy('folder_open_date',$sortType);
        }

        else{
             $queryBuilder->addOrderBy('c.case_folder_id','ASC');
        }

        return $queryBuilder;
    }

    public function countAllCaseDetails(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT c.case_id) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    
}
