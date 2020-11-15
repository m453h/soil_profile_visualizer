<?php


namespace AppBundle\Entity\Data;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Data\CaseDetailRepository")
 * @ORM\Table(name="tbl_cases")
 */
class CaseDetail
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $caseId;



    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Region")
     * @ORM\JoinColumn(name="region_code", referencedColumnName="region_code",nullable=false)
     */
    private $region;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\District")
     * @ORM\JoinColumn(name="district_code", referencedColumnName="district_code",nullable=true)
     */
    private $district;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Location\Ward")
     * @ORM\JoinColumn(name="ward_code", referencedColumnName="ward_code",nullable=true)
     */
    private $ward;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $age;


    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isLocal;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $status;



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $sex;



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $caseNumber;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Data\CaseFolder")
     * @ORM\JoinColumn(name="case_folder_id", referencedColumnName="case_folder_id",nullable=false)
     */
    private $caseFolder;




    /**
     * @return mixed
     */
    public function getCaseId()
    {
        return $this->caseId;
    }

    /**
     * @param mixed $caseId
     */
    public function setCaseId($caseId)
    {
        $this->caseId = $caseId;
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

    /**
     * @return mixed
     */
    public function getWard()
    {
        return $this->ward;
    }

    /**
     * @param mixed $ward
     */
    public function setWard($ward)
    {
        $this->ward = $ward;
    }

    /**
     * @return mixed
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param mixed $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return mixed
     */
    public function getisLocal()
    {
        return $this->isLocal;
    }

    /**
     * @param mixed $isLocal
     */
    public function setIsLocal($isLocal)
    {
        $this->isLocal = $isLocal;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCaseFolder()
    {
        return $this->caseFolder;
    }

    /**
     * @param mixed $caseFolder
     */
    public function setCaseFolder($caseFolder)
    {
        $this->caseFolder = $caseFolder;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return mixed
     */
    public function getCaseNumber()
    {
        return $this->caseNumber;
    }

    /**
     * @param mixed $caseNumber
     */
    public function setCaseNumber($caseNumber)
    {
        $this->caseNumber = $caseNumber;
    }

    /**
     * @return mixed
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * @param mixed $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }



}