<?php

namespace Kayue\WordpressBundle\Form;

use Kayue\WordpressBundle\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'author',
            TextType::class,
            [
                'label' => 'Name',
                'attr'  => ['class' => 'span4']
            ]
        );
        $builder->add(
            'authorEmail',
            EmailType::class,
            [
                'label' => 'Email',
                'attr'  => ['class' => 'span4']
            ]
        );
        $builder->add(
            'content',
            TextareaType::class,
            [
                'label' => 'Your comment',
                'attr'  => [
                    'class' => 'span6',
                    'cols'  => 30,
                    'rows'  => 10
                ]
            ]
        );
        $builder->add(
            'save',
            SubmitType::class,
            [
                'label' => 'Submit comment',
                'attr'  => ['class' => 'btn']
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => Comment::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wp_comment';
    }
}
