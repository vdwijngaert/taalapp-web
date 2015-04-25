<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'question', 'text', array(
                'label' => 'Vraag',
                'attr' => array('placeholder' => 'Bv: Wat eten we deze middag?')
            ) );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Question'
        ) );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_question';
    }
}
