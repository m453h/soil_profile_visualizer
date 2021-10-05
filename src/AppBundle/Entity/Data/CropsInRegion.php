<?php


namespace AppBundle\Entity\Data;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Data\CropsInRegionRepository")
 * @ORM\Table(name="tbl_crops_in_region")
 */
class CropsInRegion
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $recordId;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Region")
     * @ORM\JoinColumn(name="region_code", referencedColumnName="region_code",nullable=false)
     */
    private $region;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cropCategory;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cropCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $cropName;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $harvestedArea;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $productionValue;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $recordYear;

    /**
     * @return mixed
     */
    public function getRecordId()
    {
        return $this->recordId;
    }

    /**
     * @param mixed $recordId
     */
    public function setRecordId($recordId)
    {
        $this->recordId = $recordId;
    }

    /**
     * @return mixed
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

    /**
     * @return mixed
     */
    public function getCropCategory()
    {
        return $this->cropCategory;
    }

    /**
     * @param mixed $cropCategory
     */
    public function setCropCategory($cropCategory)
    {
        $this->cropCategory = $cropCategory;
    }

    /**
     * @return mixed
     */
    public function getCropCode()
    {
        return $this->cropCode;
    }

    /**
     * @param mixed $cropCode
     */
    public function setCropCode($cropCode)
    {
        $this->cropCode = $cropCode;
    }

    /**
     * @return mixed
     */
    public function getCropName()
    {
        return $this->cropName;
    }

    /**
     * @param mixed $cropName
     */
    public function setCropName($cropName)
    {
        $this->cropName = $cropName;
    }

    /**
     * @return mixed
     */
    public function getHarvestedArea()
    {
        return $this->harvestedArea;
    }

    /**
     * @param mixed $harvestedArea
     */
    public function setHarvestedArea($harvestedArea)
    {
        $this->harvestedArea = $harvestedArea;
    }

    /**
     * @return mixed
     */
    public function getProductionValue()
    {
        return $this->productionValue;
    }

    /**
     * @param mixed $productionValue
     */
    public function setProductionValue($productionValue)
    {
        $this->productionValue = $productionValue;
    }

    /**
     * @return mixed
     */
    public function getRecordYear()
    {
        return $this->recordYear;
    }

    /**
     * @param mixed $recordYear
     */
    public function setRecordYear($recordYear)
    {
        $this->recordYear = $recordYear;
    }


}