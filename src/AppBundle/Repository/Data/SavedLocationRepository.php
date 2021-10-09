<?php

namespace AppBundle\Repository\Data;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class SavedLocationRepository extends EntityRepository
{


    /**
     * @return mixed[]
     */
    public function getAllSavedPlaces()
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select('latitude,longitude,date_created,region_name,ward_name,district_name,
        st.name as "soil_type",region_image')
            ->from('tbl_saved_location', 't')
            ->join('t','spd_tanzania_wards','w','w.ward_code=t.ward_code')
            ->join('w','spd_tanzania_districts','d','d.district_code=w.district_code')
            ->join('d','spd_tanzania_regions','r','r.region_code=d.region_code')
            ->join('t','cfg_soil_types','st','st.code=t.soil_type')
            ->addOrderBy('record_id','DESC');

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }

}
