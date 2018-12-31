<?php

namespace QuizBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',TextType::class)
            ->add('questionId', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'QuizBundle\Entity\Comment',));
    }

    public function getBlockPrefix()
    {
        return 'quiz_bundle_comment_type';
    }
}
