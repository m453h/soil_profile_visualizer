<?php

namespace AppBundle\Form\Data;


use AppBundle\Entity\Data\CropsInRegion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CropsInRegionFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cropName',null,['required'=>true])
            ->add('cropCode',null,['required'=>true])
            ->add('harvestedArea',null,['required'=>true])
            ->add('productionValue',null,['required'=>true])
            ->add('recordYear',null,['required'=>true])
            ->add('cropCategory',null,['required'=>true]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>CropsInRegion::class
        ]);
    }


}