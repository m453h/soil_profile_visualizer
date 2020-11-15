<?php


namespace AppBundle\Entity\UserAccounts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Accounts\AuditTrailRepository")
 * @ORM\Table(name="log_audit_trail")
 */
class AuditTrail
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $logId;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $timeLogged;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $action;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $entityDescriptor;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $originalData;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $finalData;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $ipAddress;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $userAgent;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccounts\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=true)
     */
    private $user;

    /**
     * @return mixed
     */
    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * @param mixed $logId
     */
    public function setLogId($logId)
    {
        $this->logId = $logId;
    }

    /**
     * @return mixed
     */
    public function getTimeLogged()
    {
        return $this->timeLogged;
    }

    /**
     * @param mixed $timeLogged
     */
    public function setTimeLogged($timeLogged)
    {
        $this->timeLogged = $timeLogged;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getOriginalData()
    {
        return $this->originalData;
    }

    /**
     * @param mixed $originalData
     */
    public function setOriginalData($originalData)
    {
        $this->originalData = $originalData;
    }

    /**
     * @return mixed
     */
    public function getFinalData()
    {
        return $this->finalData;
    }

    /**
     * @param mixed $finalData
     */
    public function setFinalData($finalData)
    {
        $this->finalData = $finalData;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param mixed $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
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
    public function getEntityDescriptor()
    {
        return $this->entityDescriptor;
    }

    /**
     * @param mixed $entityDescriptor
     */
    public function setEntityDescriptor($entityDescriptor)
    {
        $this->entityDescriptor = $entityDescriptor;
    }


}