<?php

namespace Kayue\WordpressBundle\Repository;

use Kayue\WordpressBundle\Entity\Comment;
use Kayue\WordpressBundle\Entity\Post;

class CommentRepository extends AbstractRepository
{
    public function findApproved(Post $post)
    {
        return $this->getEntityManager()->getRepository($this->getEntityName())->findBy([
            'post'     => $post,
            'approved' => 1,
            'parent'   => 0,
            'type'     => '',
        ], array(
            'date' => 'ASC'
        ));
    }

    public function findChildren(Comment $comment)
    {
        return $this->getEntityManager()->getRepository($this->getEntityName())->findBy([
            'parent'   => $comment,
            'approved' => 1,
            'type'     => '',
        ], array(
            'date' => 'ASC'
        ));
    }

    public function getAlias()
    {
        return 'c';
    }
}
