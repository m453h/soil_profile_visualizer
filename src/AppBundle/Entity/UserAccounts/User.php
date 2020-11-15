<?php

namespace AppBundle\Entity\UserAccounts;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Bafford\PasswordStrengthBundle\Validator\Constraints as BAssert;



/**
 * @ORM\Entity
 * @ORM\Table(name="user_accounts")
 *@ORM\Entity(repositoryClass="AppBundle\Repository\Accounts\UserRepository")
 */
class User implements  UserInterface,EquatableInterface,\Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string",length=25,unique=true)
     */
    private $username;


    /**
     * @ORM\Column(type="string")
     */
    private $fullName;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserAccounts\UserRole", mappedBy="user")
     */
    private $userRole;


    /**
     * @ORM\Column(type="string", length=1, options={"fixed" = true})
     */
    private $accountStatus;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $mobilePhone;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $loginTries;

    /**
     * @ORM\Column(type="string")
     */
    private $password;


    private $plainPassword;

   
    private $plainPasswordConfirm;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreated;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastActivity;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $authenticationToken;
    
    private $givenNames;
    
    private $surname;

    public function __construct()
    {
        $this->userRole = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getRoles()
    {
        $roles = array();

        $arr = $this->userRole->toArray();

        foreach ($arr as $userRole)
        {
            array_push($roles,'ROLE_'.strtoupper(str_replace(' ','_',$userRole->getRole()->getRoleName())));
        }

        return $roles;
    }

    public function getRoleIds()
    {
        $roleIds = [];

        $arr = $this->userRole->toArray();

        foreach ($arr as $userRole)
        {
            array_push($roleIds,$userRole->getRole()->getRoleId());
        }

        return $roleIds;
    }

    public function getPassword()
    {
        return $this->password;
    }


    public function getSalt()
    {

    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }


    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        $this->password = null;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    public function getAccountStatus()
    {
        return $this->accountStatus;
    }
    
    public function setAccountStatus($accountStatus)
    {
        $this->accountStatus = $accountStatus;
    }

    public function getLoginTries()
    {
        return $this->loginTries;
    }
    
    public function setLoginTries($loginTries)
    {
        $this->loginTries = $loginTries;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if (!($this->getLastActivity() == $user->getLastActivity())) {
            return false;
        }

        return true;
    }


    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->lastActivity
        ));
    }


    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->lastActivity
            ) = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @param mixed $lastActivity
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }
    
    /**
     * @return mixed
     */
    public function getGivenNames()
    {
        //Check if given name and surname is null and get the data from Full name field
        if($this->givenNames===null && $this->surname===null)
        {
            $str = explode(' ', $this->fullName);

            $this->surname = array_pop($str);

            $str = implode(' ', $str);
        }
        else
        {
            $str = $this->givenNames;
        }

        return $str;
    }

    /**
     * @param mixed $givenNames
     */
    public function setGivenNames($givenNames)
    {
        $this->givenNames = $givenNames;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
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

    /**
     * @return mixed
     */
    public function getAuthenticationToken()
    {
        return $this->authenticationToken;
    }

    /**
     * @param mixed $authenticationToken
     */
    public function setAuthenticationToken($authenticationToken)
    {
        $this->authenticationToken = $authenticationToken;
    }

    /**
     * @return mixed
     */
    public function getPlainPasswordConfirm()
    {
        return $this->plainPasswordConfirm;
    }

    /**
     * @param mixed $plainPasswordConfirm
     */
    public function setPlainPasswordConfirm($plainPasswordConfirm)
    {
        $this->plainPasswordConfirm = $plainPasswordConfirm;
    }

}