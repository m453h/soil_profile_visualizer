<?php


namespace AppBundle\Form\Accounts;


use AppBundle\Entity\UserAccounts\Role;
use AppBundle\Entity\UserAccounts\User;
use AppBundle\Entity\UserAccounts\UserRole;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserFormType extends  AbstractType
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username',null,['required'=>true,'mapped'=>true])
            ->add('givenNames',null,['required'=>true, 'mapped'=>true,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]])
            ->add('surname',null,['required'=>true,'mapped'=>true,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]
            ])
            ->add('mobilePhone',null,['required'=>false,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                ]
                ])
            ->add('email',TextType::class,['required'=>false,
                'constraints'=>[
                    new NotBlank(['message'=>'This field can not be blank']),
                    new Email([
                        'message'=> "The email '{{ value }}' is not a valid email.",
                        'checkMX'=>true
                    ])
                ]]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            if(!$event->getData() instanceof User)
            {
                $form = $event->getForm();

                $form->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeated Password'),
                    'constraints'=>[
                        new NotBlank(['message'=>'This field can not be blank']),
                    ]
                ))
                    ->add('username',null,['required'=>true,'mapped'=>true]);

            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>User::class
        ]);
    }

}