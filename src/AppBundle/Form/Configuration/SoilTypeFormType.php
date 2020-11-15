<?php

namespace AppBundle\Form\Configuration;


use AppBundle\Entity\Configuration\SoilType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoilTypeFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,['required'=>true])
            ->add('code',null,['required'=>true])
            ->add('mapColor',null,['required'=>true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>SoilType::class
        ]);
    }


}