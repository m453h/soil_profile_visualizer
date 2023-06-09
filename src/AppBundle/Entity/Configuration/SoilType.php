<?php


namespace AppBundle\Entity\Configuration;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Configuration\SoilTypeRepository")
 * @ORM\Table(name="cfg_soil_types")
 */
class SoilType
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string",nullable=false)
     */
    private $code;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $name;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mapColor;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $potentialUse;



    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $limitations;


    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getMapColor()
    {
        return $this->mapColor;
    }

    /**
     * @param mixed $mapColor
     */
    public function setMapColor($mapColor)
    {
        $this->mapColor = $mapColor;
    }

    /**
     * @return mixed
     */
    public function getPotentialUse()
    {
        return $this->potentialUse;
    }

    /**
     * @param mixed $potentialUse
     */
    public function setPotentialUse($potentialUse)
    {
        $this->potentialUse = $potentialUse;
    }

    /**
     * @return mixed
     */
    public function getLimitations()
    {
        return $this->limitations;
    }

    /**
     * @param mixed $limitations
     */
    public function setLimitations($limitations)
    {
        $this->limitations = $limitations;
    }

}