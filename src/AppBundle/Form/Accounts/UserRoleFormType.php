<?php

namespace AppBundle\Form\Accounts;

use AppBundle\Entity\UserAccounts\UserRole;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserRoleFormType extends AbstractType
{

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManager $entityManager,RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $form = $event->getForm();

            $userId = $this->requestStack->getCurrentRequest()->get('Id');

            $assignedRoles = $this->entityManager->getRepository('AppBundle:UserAccounts\User')
                ->getAssignedRolesToUser($userId);

            $availableRoles = $this->entityManager->getRepository('AppBundle:UserAccounts\User')
                ->getAvailableRoles();

            $form->add('role', ChoiceType::class, array(
                'placeholder' => 'Choose a role',
                'multiple' =>true,
                'expanded'=>false,
                'attr'=>['class'=>'multi-select'],
                'choices'  => $availableRoles,
                'data' => $assignedRoles,
                'label'=>'Roles available',
                'required'=>false
            ));

        }
        );



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>UserRole::class
        ]);
    }


}