<?php

namespace Kayue\WordpressBundle\Helper;

use Kayue\WordpressBundle\Entity\Comment;
use Kayue\WordpressBundle\Entity\Post;
use Kayue\WordpressBundle\Wordpress\Helper\AttachmentHelper;

class BlogutilHelper
{
    const DEFAULT_IMAGE = 'default_image.jpg';
    /** @var string */
    private $wpUploadUrl;
    /** @var string */
    private $staticUrl;
    /** @var  AttachmentHelper */
    private $attachmentHelper;

    public function __construct(AttachmentHelper $attachmentHelper, $wpUploadUrl, $staticUrl)
    {
        $this->attachmentHelper = $attachmentHelper;
        $this->wpUploadUrl      = $wpUploadUrl;
        $this->staticUrl        = $staticUrl;
    }

    /**
     * @param array $posts
     * @return array
     */
    public function preparePostsInfoForBlogList($posts)
    {
        $preparePosts = array();

        /* @var $post Post */
        foreach ($posts as $post) {
            $temp = array();

            // Data:
            $temp['user']           = array(
                'email' => $post->getUser()->getEmail(),
                'displayname' => $post->getUser()->getDisplayName(),
                'nicename' => $post->getUser()->getNicename(),

            );
            $temp['title']          = $post->getTitle();
            $temp['content']        = $this->parseContent($post->getContent());
            $temp['slug']           = $post->getSlug();
            $temp['meta_role']      = 'Editor';
            $temp['published_date'] = $post->getDate();
            $temp['excerpt']        = $post->getExcerpt();
            $temp['comments']       = $this->getValidComments($post);
            $temp['tags']           = $this->getTags($post);
            $temp['item']           = $post;
            $temp['featured_image'] = str_replace(
                $this->wpUploadUrl,
                $this->staticUrl,
                $this->attachmentHelper->findThumbnail($post)->getGuid()
            );

            $preparePosts[] = $temp;
        }

        return $preparePosts;
    }

    /**
     * @param $post
     */
    private function getTags($post)
    {
        $taxonomies = $post->getTaxonomies();
        $tags = array();

        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy->getName() == 'post_tag') {
                $tags[] = $taxonomy->getTerm()->getName();
            }
        }

        return $tags;
    }

    /**
     * Remove links and other html / javascript stuff from the user input.
     *
     * @param $content
     * @return mixed|string
     */
    public function cleanCommentContent($content)
    {
        $content = filter_var($content, FILTER_SANITIZE_STRING);

        if (strlen($content) == 0) {
            $content = 'Content removed for security reasons.';
        }

        return $content;
    }

    protected function getValidComments(Post $post)
    {
        $comments    = array();
        $allComments = $post->getComments();
        /** @var Comment $comment */
        foreach ($allComments as $comment) {
            if ($comment->getApproved() === "1") {
                $comments[] = $comment;
            }
        }

        return $comments;
    }

    protected function parseContent($content)
    {
        $content       = explode("\n\r", $content);
        $final_content = '';
        foreach ($content as $item) {
            if (preg_match('/\[caption/', $item)) {
                $item = '<hr>' . $this->processCaption($item);
            }
            $final_content .= '<p>' . $item . '</p>';
        }

        return $final_content;
    }

    protected function processCaption($item)
    {
        $parsed_item = '';
        // '<img class="post-img" src="//{{static_url}}/{{ post.featured_image.url }}" alt="">';
        preg_match_all('/(\[caption.*\])(.*)(\[.*\])/', $item, $parts);
        if (isset($parts[2][0])) {
            $parsed_item = str_replace(
                $this->wpUploadUrl,
                $this->staticUrl,
                $parts[2][0]
            );
        }

        return $parsed_item;
    }
}