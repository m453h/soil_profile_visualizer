<?php

namespace AppBundle\Form\Data;


use AppBundle\Entity\Data\CaseFolder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CaseFolderFormType extends  AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('folderOpenDate', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'date'],
                'html5' => false,
            ])
            ->add('folderLetterFile', VichFileType::class, array(
                'required'      => true,
                'allow_delete'  => false, // not mandatory, default is true
                'download_link' => false, // not mandatory, default is true
                'attr'=>['class'=>'inputFile'],
                'label'=>'Case File'))
            ->add('folderVideoURL',null,['required'=>false,'label'=>'Video URL']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' =>CaseFolder::class
        ]);
    }


}