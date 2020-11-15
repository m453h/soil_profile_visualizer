<?php

namespace AppBundle\Repository\Data;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class CaseFolderRepository extends EntityRepository
{

    
    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllCaseFolders($options=[])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('c.case_folder_id,c.folder_open_date')
            ->from('tbl_case_folder', 'c');

        $queryBuilder = $this->setFilterOptions($options,$queryBuilder);
        $queryBuilder = $this->setSortOptions($options,$queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if(isset($options['type']))
        {
            $queryBuilder->select('c.case_folder_id,c.folder_open_date')
                ->addSelect('folder_letter_name','folder_video_url')
                ->addOrderBy('c.folder_open_date','DESC');

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
             $queryBuilder->addOrderBy('c.folder_open_date','DESC');
        }

        return $queryBuilder;
    }

    public function countAllCaseFolders(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT c.case_folder_id) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }

    public function getStatistics()
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select("SUM (CASE WHEN status = '1' THEN 1 ELSE 0 END) AS \"Total\",
                               SUM (CASE WHEN status = '2' THEN 1 ELSE 0 END) AS \"Recovered\",
                               SUM (CASE WHEN status = '3' THEN 1 ELSE 0 END) AS \"Fatal\"")
            ->from('tbl_cases', 'c');


        $statistics =  $queryBuilder->execute()->fetch();
        $statistics['Active']=$statistics['Total']-($statistics['Recovered']+$statistics['Fatal']);
        return $statistics;
    }


    public function getDailyStatistics()
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select("TO_CHAR(folder_open_date,'dd-mm-YYYY') AS summary_date,
                                SUM (CASE WHEN status = '1' THEN 1 ELSE 0 END) AS \"Total\",
                               SUM (CASE WHEN status = '2' THEN 1 ELSE 0 END) AS \"Recovered\",
                               SUM (CASE WHEN status = '3' THEN 1 ELSE 0 END) AS \"Fatal\""

                            )
            ->from('tbl_cases', 'c')
            ->join('c','tbl_case_folder','f','f.case_folder_id=c.case_folder_id')
            ->groupBy('folder_open_date')
            ->addOrderBy('folder_open_date','ASC');

        $records =  $queryBuilder->execute()->fetchAll();
        $statistics = [];

        $cumulativeTotal = 0;
        $cumulativeFatal = 0;
        $cumulativeRecovered= 0;

        $graphDates = [];
        $graphCumulativeTotal = [];
        $graphCumulativeFatal = [];
        $graphCumulativeRecovered = [];
        $graphDailyCases = [];

        foreach ($records as $data)
        {
            $cumulativeTotal = $cumulativeTotal + $data['Total'];
            $cumulativeFatal = $cumulativeFatal + $data['Fatal'];
            $cumulativeRecovered = $cumulativeRecovered + $data['Recovered'];


            array_push($graphDates,'"'.$data['summary_date'].'"');
            array_push($graphCumulativeTotal,$cumulativeTotal);
            array_push($graphCumulativeFatal,$cumulativeFatal);
            array_push($graphCumulativeRecovered,$cumulativeRecovered);
            array_push($graphDailyCases,$data['Total']);
        }

        $graphData['cumulativeTotals'] = implode(',',$graphCumulativeTotal);
        $graphData['cumulativeFatal'] = implode(',',$graphCumulativeFatal);
        $graphData['cumulativeRecovered'] = implode(',',$graphCumulativeRecovered);
        $graphData['dailyCases'] = implode(',',$graphDailyCases);
        $graphData['dates'] = implode(',',$graphDates);

        return $graphData;
    }




}
