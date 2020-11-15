<?php

namespace AppBundle\Repository\Accounts;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    /**
     * @param array $options
     * @return QueryBuilder
     */
    public function findAllUsers($options = [])
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->select("id,username,full_name,mobile_phone,account_status,
        array_agg(role_name) AS roles")
            ->from('user_accounts', 'u')
            ->leftJoin('u','user_roles','ur','ur.user_id=u.id')
            ->leftJoin('ur','user_defined_roles','r','ur.role_id=r.role_id')
            ->groupBy('u.id');
        $queryBuilder = $this->setFilterOptions($options, $queryBuilder);
        $queryBuilder = $this->setSortOptions($options, $queryBuilder);

        return $queryBuilder;
    }

    public function setFilterOptions($options, QueryBuilder $queryBuilder)
    {
        if (!empty($options['username']))
        {
            return $queryBuilder->andwhere('u.username LIKE :username')
                ->setParameter('username', '%' . $options['username'] . '%');
        }

        return $queryBuilder;
    }

    public function setSortOptions($options, QueryBuilder $queryBuilder)
    {

        $options['sortType'] == 'desc' ? $sortType = 'desc' : $sortType = 'asc';

        if ($options['sortBy'] === 'username')
        {
            return $queryBuilder->addOrderBy('u.username', $sortType);
        }

        return $queryBuilder->addOrderBy('u.id', 'desc');

    }

    public function countAllUsers(QueryBuilder $queryBuilder)
    {
        return function ($queryBuilder) {
            $queryBuilder->select('COUNT(DISTINCT id) AS total_results')
                ->resetQueryPart('orderBy')
                ->resetQueryPart('groupBy')
                ->setMaxResults(1);
        };
    }


    public function getAssignedRolesToUser($userId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $results = $queryBuilder->select('role_id')
            ->from('user_roles', 'r')
            ->andWhere('user_id=:userId')
            ->setParameter('userId',$userId);

        $results = $results->execute()
            ->fetchAll();

        $myArray = [];

        foreach ($results as $data)
        {
            array_push($myArray,$data['role_id']);
        }

        return $myArray;
    }

    public function getAvailableRoles()
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);

        $queryBuilder->select('r.role_id AS "value", r.role_name AS "name"')
            ->from('user_defined_roles', 'r');

        $results = $queryBuilder ->execute()
            ->fetchAll();

        $myArray = [];

        foreach ($results as $data)
        {
            $myArray[$data['name']]=$data['value'];
        }

        return $myArray;
    }


    public function deleteUserRole($Id)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->delete('user_roles')
            ->andWhere('user_id=?')
            ->setParameter(0,$Id);
        $queryBuilder->execute();
    }

    public function recordUserRole($roleId,$userId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $queryBuilder->insert('user_roles')
            ->setValue('role_id','?')
            ->setValue('user_id','?')
            ->setParameter(0,$roleId)
            ->setParameter(1,$userId);
        $queryBuilder->execute();
    }

    public function findCurrentUserHash($Id)
    {

        $conn = $this->getEntityManager()->getConnection();

        $queryBuilder = new QueryBuilder($conn);
        $results = $queryBuilder->select('password')
            ->from('user_accounts', 'u')
            ->where('u.id = :Id')
            ->setParameter('Id', $Id)
            ->execute()
            ->fetch();

        return $results['password'];
    }



}
