<?php
/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/5/15
 * Time: 10:28 AM
 */

namespace P5\Repository;

use Doctrine\ORM\EntityRepository;

class DocumentRepository extends EntityRepository
{
    public function getMyDocuments($user)
    {
        return $this->createQueryBuilder('d')
            ->where("d.user = :user")
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}