<?php

namespace AppBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class BlogPostRepository
 * @package AppBundle\Entity\Repository
 */
class BlogPostRepository extends EntityRepository
{
    /**
     * @param int $id
     * @return \Doctrine\ORM\Query
     */
    public function createFindOneByIdQuery(int $id)
    {
        $query = $this->_em->createQuery(
            "
            SELECT bp
            FROM AppBundle:BlogPost bp
            WHERE bp.id = :id
            "
        );

        $query->setParameter('id', $id);

        return $query;
    }

    /**
     * @return \Doctrine\ORM\Query
     */
    public function createFindAllQuery()
    {
        return $this->_em->createQuery(
            "
            SELECT bp
            FROM AppBundle:BlogPost bp
            "
        );
    }
}