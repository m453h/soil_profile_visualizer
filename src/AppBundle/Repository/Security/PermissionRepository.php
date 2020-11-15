<?php

namespace AppBundle\Repository\Security;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class PermissionRepository extends EntityRepository
{
    /**
     * @param $object
     * @param $userRoles
     * @return QueryBuilder
     */
    public function getCurrentUserACLs($object,$userRoles)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $results = $queryBuilder->select('actions')
            ->from('user_roles_permissions', 'd');
        
        $counter = 0;

        foreach ($userRoles as $userRole)
        {
            $results->orWhere("role_id=:role_id_$counter")
                ->setParameter(":role_id_$counter", $userRole);

            $counter++;
        }

        $results=$results->andWhere('object=:object')
            ->setParameter(':object',$object)
            ->execute()
            ->fetch();

        $ACLs = json_decode($results['actions']);

        return $ACLs;
    }

    /**
     * @param $object
     * @param $roleId
     * @param $actions
     */
    public function recordPermission($object,$roleId,$actions)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder
            ->insert('user_roles_permissions')
            ->setValue('object',':object')
            ->setValue('role_id',':roleId')
            ->setValue('actions',':actions')
            ->setParameter('object',$object)
            ->setParameter('roleId',$roleId)
            ->setParameter('actions',$actions)
            ->execute();
    }

    /**
     * @param $roleId
     * @return string
     */
    public function clearPermissionByRoleId($roleId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

         $queryBuilder
             ->delete('user_roles_permissions')
             ->where('role_id= :roleId')
             ->setParameter('roleId',$roleId)
             ->execute();
    }


}
