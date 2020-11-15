<?php


namespace AppBundle\Entity\Location;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\DistrictRepository")
 * @ORM\Table(name="spd_tanzania_districts",uniqueConstraints={@ORM\UniqueConstraint(name="unique_district_code", columns={"district_code"})})
 */
class District
{


    /**
     * @ORM\Column(type="integer")
     */
    private $gid;


    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $districtCode;
    

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $districtName;

    
    /**
     * @ORM\Column(name="district_geometry", type="geometry", options={"geometry_type"="MULTIPOLYGON", "srid"=3795}, nullable=true)
     */
    private $districtGeometry;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Region")
     * @ORM\JoinColumn(name="region_code", referencedColumnName="region_code",nullable=false)
     */
    private $region;


    /**
     * @return mixed
     */
    public function getDistrictName()
    {
        return $this->districtName;
    }

    /**
     * @param mixed $districtName
     */
    public function setDistrictName($districtName)
    {
        $this->districtName = $districtName;
    }

    /**
     * @return mixed
     */
    public function getDistrictCode()
    {
        return $this->districtCode;
    }

    /**
     * @param mixed $districtCode
     */
    public function setDistrictCode($districtCode)
    {
        $this->districtCode = $districtCode;
    }

    /**
     * @return mixed
     */
    public function getDistrictGeometry()
    {
        return $this->districtGeometry;
    }

    /**
     * @param mixed $districtGeometry
     */
    public function setDistrictGeometry($districtGeometry)
    {
        $this->districtGeometry = $districtGeometry;
    }

    /**
     * @return mixed
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * @param mixed $gid
     */
    public function setGid($gid)
    {
        $this->gid = $gid;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getDistrictLabel(){

        $region = $this->getRegion();

        if ($region instanceof Region)
        {
            return $region->getRegionName().' - '.$this->districtName;
        }

        return $this->districtName;
    }

}