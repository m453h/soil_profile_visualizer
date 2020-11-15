<?php


namespace AppBundle\Entity\Location;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\WardRepository")
 * @ORM\Table(name="spd_tanzania_wards",uniqueConstraints={@ORM\UniqueConstraint(name="unique_ward_code", columns={"ward_code"})})
 */
class Ward
{

    /**
     * @ORM\Column(type="integer")
     */
    private $gid;


    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $wardCode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $wardName;


    /**
     * @ORM\Column(name="ward_geometry", type="geometry", options={"geometry_type"="MULTIPOLYGON", "srid"=3795}, nullable=true)
     */
    private $wardGeometry;



    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\District")
     * @ORM\JoinColumn(name="district_code", referencedColumnName="district_code",nullable=false)
     */
    private $district;

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
     * @return mixed
     */
    public function getWardCode()
    {
        return $this->wardCode;
    }

    /**
     * @param mixed $wardCode
     */
    public function setWardCode($wardCode)
    {
        $this->wardCode = $wardCode;
    }

    /**
     * @return mixed
     */
    public function getWardName()
    {
        return $this->wardName;
    }

    /**
     * @param mixed $wardName
     */
    public function setWardName($wardName)
    {
        $this->wardName = $wardName;
    }

    /**
     * @return mixed
     */
    public function getWardGeometry()
    {
        return $this->wardGeometry;
    }

    /**
     * @param mixed $wardGeometry
     */
    public function setWardGeometry($wardGeometry)
    {
        $this->wardGeometry = $wardGeometry;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    public function getWardLabel(){

        $district = $this->getDistrict();

        if ($district instanceof District)
        {
            $region = $district->getRegion();

            if($region instanceof Region)
            {
                return $region->getRegionName().' - '.$district->getDistrictName().'-'.$this->getWardName();
            }
        }

        return $this->wardName;
    }

}