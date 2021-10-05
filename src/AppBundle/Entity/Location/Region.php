<?php


namespace AppBundle\Entity\Location;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Location\RegionRepository")
 * @ORM\Table(name="spd_tanzania_regions",uniqueConstraints={@ORM\UniqueConstraint(name="unique_region_code", columns={"region_code"})})
 */
class Region
{


    /**
     * @ORM\Column(type="integer")
     */
    private $gid;


    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $regionCode;
    

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $regionName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $recommendedCrops;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $regionImage;



    /**
     * @ORM\Column(name="region_geometry", type="geometry", options={"geometry_type"="MULTIPOLYGON", "srid"=3795}, nullable=true)
     */
    private $regionGeometry;


    /**
     * @return mixed
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * @param mixed $regionName
     */
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;
    }

    /**
     * @return mixed
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * @param mixed $regionCode
     */
    public function setRegionCode($regionCode)
    {
        $this->regionCode = $regionCode;
    }

    /**
     * @return mixed
     */
    public function getRegionGeometry()
    {
        return $this->regionGeometry;
    }

    /**
     * @param mixed $regionGeometry
     */
    public function setRegionGeometry($regionGeometry)
    {
        $this->regionGeometry = $regionGeometry;
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
     * @return mixed
     */
    public function getRecommendedCrops()
    {
        return $this->recommendedCrops;
    }

    /**
     * @param mixed $recommendedCrops
     */
    public function setRecommendedCrops($recommendedCrops)
    {
        $this->recommendedCrops = $recommendedCrops;
    }

    /**
     * @return mixed
     */
    public function getRegionImage()
    {
        return $this->regionImage;
    }

    /**
     * @param mixed $regionImage
     */
    public function setRegionImage($regionImage)
    {
        $this->regionImage = $regionImage;
    }


}