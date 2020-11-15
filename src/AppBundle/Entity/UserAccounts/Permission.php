<?php


namespace AppBundle\Entity\UserAccounts;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Security\PermissionRepository")
 * @ORM\Table(name="user_roles_permissions")
 */
class Permission
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $permissionID;


    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $object;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAccounts\Role", inversedBy="permissions")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="role_id",nullable=false,onDelete="CASCADE")
     */
    private $role;


    /**
     * @ORM\Column(type="json_array")
     */
    private $actions;

    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param mixed $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
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

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getPermissionID()
    {
        return $this->permissionID;
    }


}