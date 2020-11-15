<?php


namespace AppBundle\Entity\UserAccounts;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Accounts\RoleRepository")
 * @ORM\Table(name="user_defined_roles")
 */
class Role
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $roleID;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $roleName;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\UserAccounts\Permission", mappedBy="role")
     */
    private $permissions;

    
    /**
     * @return ArrayCollection|Permission[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
    
    
    /**
     * @return mixed
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * @param mixed $roleName
     */
    public function setRoleName($roleName)
    {
        $this->roleName = $roleName;
    }

    /**
     * @return mixed
     */
    public function getRoleID()
    {
        return $this->roleID;
    }

    /**
     * @param mixed $roleID
     */
    public function setRoleID($roleID)
    {
        $this->roleID = $roleID;
    }


}