<?php

namespace AppBundle\Form\Accounts;


use AppBundle\Entity\UserAccounts\Permission;
use AppBundle\Form\EventListener\AddPermissionDataRolePermissionForm;
use AppBundle\Helpers\FileLoader;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolePermissionFormType extends  AbstractType
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FileLoader
     */
    private $fileLoader;
    /**
     * @var
     */
    private $permissionFile;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManager $entityManager, FileLoader $fileLoader,$permissionFile,RequestStack $requestStack)
    {
        $this->em = $entityManager;
        $this->fileLoader = $fileLoader;
        $this->permissionFile = $permissionFile;
        $this->requestStack = $requestStack;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('role',RoleFormType::class)
            ->addEventSubscriber(new AddPermissionDataRolePermissionForm($this->em,
                $this->fileLoader,$this->permissionFile,$this->requestStack));


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>Permission::class
        ]);
    }


}