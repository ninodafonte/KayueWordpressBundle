<?php

namespace Kayue\WordpressBundle\Controller;

use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Entity\PostRepository;
use Kayue\WordpressBundle\Form\CommentType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Kayue\WordpressBundle\Entity\Comment;

class PostController extends Controller
{
    /**
     * @param int $limit
     * @return array
     */
    public function getLastPosts($limit = 5)
    {
        // Get the last posts:
        $repo  = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');
        $posts = $repo->findBy(
            array(
                'type'   => 'post',
                'status' => 'publish',
            ),
            array('date' => 'desc'),
            $limit
        );

        $blogUtil = $this->get('kayue_wordpress.blog_util');
        $posts    = $blogUtil->preparePostsInfoForBlogList($posts);

        return $posts;
    }

    /**
     * @param Request $request
     * @param         $slug
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function getPostBySlug(Request $request, $slug)
    {
        $repo = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');
        /** @var Post $wpPost */
        $wpPost = $repo->findOneBy(
            array(
                'slug' => $slug
            )
        );

        if (is_null($wpPost)) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $blogUtil = $this->get('kayue_wordpress.blog_util');
        $post     = $blogUtil->preparePostsInfoForBlogList(array($wpPost))[0];

        return $post;
    }

    /**
     * @param $tag_name
     * @return array
     */
    public function getPostsByTag($tag_name)
    {
        /** @var PostRepository $em */
        $em    = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');
        $posts = $em->findAllByTag($tag_name);

        if (count($posts) == 0) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $blogUtil = $this->get('kayue_wordpress.blog_util');
        $posts    = $blogUtil->preparePostsInfoForBlogList($posts);

        return $posts;
    }


    /**
     * @param $author
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPostsByAuthor($author)
    {
        /** @var PostRepository $em */
        $em    = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');
        $posts = $em->findAllByAuthor($author);

        if (count($posts) == 0) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $blogUtil = $this->get('kayue_wordpress.blog_util');
        $posts    = $blogUtil->preparePostsInfoForBlogList($posts);

        return $posts;
    }

    /**
     * @return array
     */
    public function getAllPosts()
    {
        // Get the last posts:
        $repo  = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');
        $posts = $repo->findBy(
            array(
                'type'   => 'post',
                'status' => 'publish',
            ),
            array('date' => 'desc')
        );

        $blogUtil = $this->get('kayue_wordpress.blog_util');
        $posts    = $blogUtil->preparePostsInfoForBlogList($posts);

        return $posts;
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getPopularTags($limit = 10)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getRepository('KayueWordpressBundle:Term');
        $query = $em->createQueryBuilder('t')
            ->innerJoin('t.taxonomy', 'x', 'WITH', 'x.name = :taxonomy')
            ->addSelect('x.count, t.name, t.slug')
            ->addGroupBy('t.name')
            ->addOrderBy('x.count', 'desc')
            ->addOrderBy('t.name')
            ->setParameter('taxonomy', 'post_tag')
            ->setMaxResults($limit);

        $terms = $query->getQuery()->getResult();

        return $terms;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function getPopularPosts($limit = 3, $offset = 0)
    {
        // Get the last posts:
        $repo = $this->getDoctrine()->getRepository('KayueWordpressBundle:Post');
        $posts = $repo->findBy(
            array(
                'type' => 'post',
                'status' => 'publish',
            ),
            array('date' => 'desc'),
            $limit,
            $offset
        );

        $blogHelper = $this->get('kayue_wordpress.blog_util');
        $posts = $blogHelper->preparePostsInfoForBlogList($posts);

        return $posts;
    }
}