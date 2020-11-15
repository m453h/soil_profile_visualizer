<?php

namespace AppBundle\Form\CustomField;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


use Symfony\Component\Form\AbstractType;

class GenderType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'placeholder' => 'Select sex of the individual',
            'choices' => array(
                'Male' => 'M',
                'Female' => 'F',
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }


}