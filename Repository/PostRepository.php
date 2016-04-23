<?php

namespace Kayue\WordpressBundle\Repository;

use Kayue\WordpressBundle\Entity\Post;

class PostRepository extends AbstractRepository
{
    public function findAttachmentsByPost(Post $post)
    {
        return $this->getQueryBuilder()
            ->andWhere('p.type = :type AND p.parent = :post')
            ->setParameter('type', 'attachment')
            ->setParameter('post', $post)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAttachmentById($id)
    {
        return $this->getQueryBuilder()
            ->where('p.type = :type AND p.id = :id')
            ->setParameter('type', 'attachment')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllByTag($tag_name)
    {
        /** @var EntityManager $em */
        $em    = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT p
                FROM KayueWordpressBundle:Post p
                INNER JOIN p.taxonomies t
                INNER JOIN t.term te
                WHERE p.type = 'post'
                    AND p.status = 'publish'
                    AND t.name = 'post_tag'
                    AND te.slug = :tag_name
                ORDER BY p.date desc"
        );

        $query->setParameter('tag_name', $tag_name);

        return $query->getResult();
    }

    public function findAllByAuthor($author)
    {
        /** @var EntityManager $em */
        $em    = $this->getEntityManager();
        $query = $em->createQuery(
            "SELECT p
                FROM KayueWordpressBundle:Post p
                INNER JOIN p.user u

                WHERE p.type = 'post'
                    AND p.status = 'publish'
                    AND u.nicename = :author
                ORDER BY p.date desc"
        );

        $query->setParameter('author', $author);

        return $query->getResult();
    }

    public function getAlias()
    {
        return 'p';
    }
}
