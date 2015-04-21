<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $em = $options['em'];

        $parentTransformer = new EntityToIdTransformer( $em, 'AppBundle:Category' );
        $userTransformer = new EntityToIdTransformer( $em, 'AppBundle:User' );
        $iconTransformer = new EntityToIdTransformer( $em, 'AppBundle:Icon' );

        $builder
            ->add( 'name', 'text',
                array( 'label' => "Naam van de categorie:", 'attr' => array( 'placeholder' => 'Bv: Hobby\'s' ) ) )
            ->add( $builder->create( 'icon', 'hidden' )->addModelTransformer( $iconTransformer ) )
            ->add( $builder->create( 'parent', 'hidden' )->addModelTransformer( $parentTransformer ) )
            ->add( $builder->create( 'user', 'hidden' )->addModelTransformer( $userTransformer ) );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions( OptionsResolverInterface $resolver )
    {
        $resolver->setDefaults( array(
            'data_class' => 'AppBundle\Entity\Category'
        ) )
                 ->setRequired( array( 'em' ) )
                 ->setAllowedTypes( 'em', 'Doctrine\Common\Persistence\ObjectManager' );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_category';
    }
}
