<?php


namespace AppBundle\Entity\UserAccounts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_roles")
 */
class UserRole
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $roleNo;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccounts\User",inversedBy="userRole")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",nullable=false)
     */
    private $user;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccounts\Role",fetch="EAGER")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="role_id",nullable=true)
     */
    private $role;

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
    public function getRoleNo()
    {
        return $this->roleNo;
    }

    /**
     * @param mixed $roleNo
     */
    public function setRoleNo($roleNo)
    {
        $this->roleNo = $roleNo;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


}