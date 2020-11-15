<?php

namespace AppBundle\Form\Data;


use AppBundle\Entity\Data\CaseDetail;
use AppBundle\Entity\Location\Region;
use AppBundle\Form\CustomField\GenderType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaseDetailFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('caseNumber',null,['required'=>false,'label'=>'Case number'])
            ->add('region', EntityType::class, [
                'placeholder' => 'Choose a region',
                'choice_label' => 'regionName',
                'attr'=>['class'=>'select2-basic'],
                'mapped'=>true,
                'class' => 'AppBundle\Entity\Location\Region',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.regionName', 'ASC');
                },
                'choice_value' =>'regionCode'
            ])
            ->add('district', EntityType::class, [
                'placeholder' => 'Choose a district',
                'choice_label' => 'districtLabel',
                'attr'=>['class'=>'select2-basic'],
                'mapped'=>true,
                'class' => 'AppBundle\Entity\Location\District',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('d')
                        ->join('d.region','r')
                        ->orderBy('r.regionName', 'ASC');
                },
                'choice_value' =>'districtCode'
            ])
            ->add('ward', EntityType::class, [
                'placeholder' => 'Choose a ward',
                'choice_label' => 'wardLabel',
                'attr'=>['class'=>'select2-basic'],
                'mapped'=>true,
                'class' => 'AppBundle\Entity\Location\Ward',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('w')
                        ->join('w.district','d')
                        ->join('d.region','r')
                        ->orderBy('r.regionName', 'ASC');
                },
                'choice_value' =>'wardCode'
            ])
            ->add('status', ChoiceType::class, array(
                'placeholder' => 'Choose Status',
                'choices' => [
                    "Active"=>1,
                    "Recovered"=>2,
                    "Fatal"=>3
                ],
                'mapped' => true,
                'required' => false
            ))
            ->add('country', EntityType::class, [
                'placeholder' => 'Country travelling from',
                'choice_label' => 'countryName',
                'required'=>false,
                'attr'=>['class'=>'select2-basic'],
                'class' => 'AppBundle\Entity\Configuration\Country',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.countryName', 'ASC');
                }
            ])
            ->add('nationality', EntityType::class, [
                'placeholder' => 'SoilType',
                'choice_label' => 'nationalityName',
                'attr'=>['class'=>'select2-basic'],
                'class' => 'AppBundle\Entity\Configuration\SoilType',
                'query_builder' => function(EntityRepository  $er) {
                    return $er->createQueryBuilder('n')
                        ->orderBy('n.isDefault', 'ASC')
                        ->addOrderBy('n.nationalityName','ASC');
                }
            ])
            ->add('sex',GenderType::class,['required'=>true])
            ->add('isLocal',null,['required'=>false,'label'=>'Mark this record as local transmission'])
            ->add('age',null,['required'=>false,'label'=>'Age']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>CaseDetail::class
        ]);
    }


}