<?php
/**
 * Created by PhpStorm.
 * User: thaiht
 * Date: 10/5/15
 * Time: 10:28 AM
 */

namespace P5\Repository;

use Doctrine\ORM\EntityRepository as DoctrineORMEntityRepository;

class DocumentRepository extends DoctrineORMEntityRepository
{
    /*
     * Get all authors in the document table
     */
    public function getAllAuthors(){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('u.id, u.username')->distinct(true)
            ->from('P5\Model\User', 'u')
            ->join('P5\Model\Document', 'd', 'WITH', 'd.user=u')
            ->getQuery()->getArrayResult();

        return $results;
    }

    /*
     * Get all folder in the document table
     */
    public function getAllFolders(){
        $qb = $this->getEntityManager()->createQueryBuilder();
        $results = $qb->select('f.id, f.name')->distinct(true)
            ->from('P5\Model\Folder', 'f')
            ->join('P5\Model\Document', 'd', 'WITH', 'd.folder=f')
            ->getQuery()->getArrayResult();

        return $results;
    }
}