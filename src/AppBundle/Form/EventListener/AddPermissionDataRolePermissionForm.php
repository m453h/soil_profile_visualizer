<?php


namespace AppBundle\Form\EventListener;

use AppBundle\Form\Accounts\RoleFormType;
use AppBundle\Form\CustomField\IgnoreChoiceType;
use AppBundle\Form\DataTransformer\UserToNumberTransformer;
use AppBundle\Helpers\FileLoader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class AddPermissionDataRolePermissionForm implements EventSubscriberInterface
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

    public function __construct(EntityManager $em,FileLoader $fileLoader,$permissionFile,RequestStack $requestStack)
    {
        $this->em = $em;
        $this->fileLoader = $fileLoader;
        $this->permissionFile = $permissionFile;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {

        $form = $event->getForm();

        $data = $this->fileLoader->loadFile($this->permissionFile);

        $roleId = $this->requestStack->getCurrentRequest()->get('roleId');

        $selectedPermissions = new ArrayCollection();

        if($roleId != null)
        {
            $role = $this->em->getRepository('AppBundle:UserAccounts\Role')
                ->findOneBy(['roleID' => $roleId]);

            $availablePermissions = $permissions=$role->getPermissions();

            foreach ($availablePermissions as $availablePermission)
            {
                $selectedPermissions->set($availablePermission->getObject(),$availablePermission->getActions());
            }

            $form->add('role',RoleFormType::class,['data'=>$role]);

        }


        if($data!=null)
        {
            foreach ($data as $menu)
            {
                foreach ($menu as $item)
                {
                    $actionChoices = array();

                    foreach($item['actions'] as $action)
                    {
                        array_push($actionChoices,[ucfirst($action) =>($action)]);
                    }

                    if($selectedPermissions !=null)
                    {
                        $data = $selectedPermissions->get($item['roleClass']);
                    }


                    $form->add($item['key'], IgnoreChoiceType::class, array(
                        'multiple'=>true,
                        'expanded'=>true,
                        'choices'  => $actionChoices,
                        'data'=>$data
                    ));
                }
            }

        }



    }
    

}