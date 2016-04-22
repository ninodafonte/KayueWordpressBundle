<?php

namespace Kayue\WordpressBundle\Form;

use Symfony\Component\Form\AbstractType;
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
            'text',
            [
                'label' => 'Name',
                'attr'  => ['class' => 'span4']
            ]
        );
        $builder->add(
            'authorEmail',
            'email',
            [
                'label' => 'Email',
                'attr'  => ['class' => 'span4']
            ]
        );
        $builder->add(
            'content',
            'textarea',
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
            'submit',
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
            'data_class'      => 'Kayue\WordpressBundle\Entity\Comment',
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
