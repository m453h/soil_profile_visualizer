<?php

namespace AppBundle\Form\Location;


use AppBundle\Entity\Location\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegionFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       // $builder
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>Region::class
        ]);
    }


}