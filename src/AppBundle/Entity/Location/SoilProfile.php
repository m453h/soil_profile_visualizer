<?php


namespace AppBundle\Entity\Location;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="SoilTypeRepository")
 * @ORM\Table(name="spd_tanzania_soil_profile")
 */
class SoilProfile
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $gid;

    /**
     * @ORM\Column(type="decimal")
     */
    private $area;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $perimeter;

    /**
     * @ORM\Column(name="geom", type="geometry", options={"geometry_type"="MULTIPOLYGON", "srid"=3795}, nullable=true)
     */
    private $geometry;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $soilType;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mainType;

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
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param mixed $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return mixed
     */
    public function getPerimeter()
    {
        return $this->perimeter;
    }

    /**
     * @param mixed $perimeter
     */
    public function setPerimeter($perimeter)
    {
        $this->perimeter = $perimeter;
    }

    /**
     * @return mixed
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @param mixed $geometry
     */
    public function setGeometry($geometry)
    {
        $this->geometry = $geometry;
    }

    /**
     * @return mixed
     */
    public function getSoilType()
    {
        return $this->soilType;
    }

    /**
     * @param mixed $soilType
     */
    public function setSoilType($soilType)
    {
        $this->soilType = $soilType;
    }

    /**
     * @return mixed
     */
    public function getMainType()
    {
        return $this->mainType;
    }

    /**
     * @param mixed $mainType
     */
    public function setMainType($mainType)
    {
        $this->mainType = $mainType;
    }


}