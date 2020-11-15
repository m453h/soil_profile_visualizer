<?php


namespace AppBundle\Entity\Data;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Data\CaseFolderRepository")
 * @ORM\Table(name="tbl_case_folder")
 *@Vich\Uploadable
 */
class CaseFolder
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $caseFolderId;


    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $folderOpenDate;



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $folderLetterName;



    /**
     * @Vich\UploadableField(mapping="official_letter_file", fileNameProperty="folderLetterName")
     * @var File
     */
    private $folderLetterFile;


    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @var \DateTime
     */
    private $updatedAt;



    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $folderVideoURL;

    
    /**
     * @return mixed
     */
    public function getFolderOpenDate()
    {
        return $this->folderOpenDate;
    }

    /**
     * @param mixed $folderOpenDate
     */
    public function setFolderOpenDate($folderOpenDate)
    {
        $this->folderOpenDate = $folderOpenDate;
    }

    /**
     * @return mixed
     */
    public function getCaseFolderId()
    {
        return $this->caseFolderId;
    }

    /**
     * @param mixed $caseFolderId
     */
    public function setCaseFolderId($caseFolderId)
    {
        $this->caseFolderId = $caseFolderId;
    }

    /**
     * @return mixed
     */
    public function getFolderVideoURL()
    {
        return $this->folderVideoURL;
    }

    /**
     * @param mixed $folderVideoURL
     */
    public function setFolderVideoURL($folderVideoURL)
    {
        $this->folderVideoURL = $folderVideoURL;
    }

    /**
     * @return mixed
     */
    public function getFolderLetterName()
    {
        return $this->folderLetterName;
    }

    /**
     * @param mixed $folderLetterName
     */
    public function setFolderLetterName($folderLetterName)
    {
        $this->folderLetterName = $folderLetterName;
    }

    /**
     * @return File
     */
    public function getFolderLetterFile()
    {
        return $this->folderLetterFile;
    }

    /**
     * @param File $folderLetterFile
     */
    public function setFolderLetterFile(File $folderLetterFile = null)
    {
        $this->folderLetterFile = $folderLetterFile;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($folderLetterFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }


}