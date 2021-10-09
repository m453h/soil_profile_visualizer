<?php


namespace AppBundle\Entity\Location;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Data\SavedLocationRepository")
 * @ORM\Table(name="tbl_saved_location",uniqueConstraints={@ORM\UniqueConstraint(name="unique_location_per_user", columns={"latitude","longitude","user_id"})})
 */
class SavedLocation
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $recordId;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccounts\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=false)
     */
    private $user;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $latitude;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $wardCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $soilType;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateCreated;

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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

}