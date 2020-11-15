<?php

namespace AppBundle\Controller\UserAccounts;

use AppBundle\Entity\UserAccounts\User;
use AppBundle\Form\Accounts\ResetPasswordForm;
use AppBundle\Form\Accounts\UserFormType;
use AppBundle\Form\Accounts\UserRoleFormType;
use Doctrine\DBAL\Driver\PDOException;
use Exception;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class UserController extends Controller
{

    /**
     * @Route("/administration/user-accounts", name="portal_users_list")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function listAction(Request $request)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('view',$class);
        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['username'] = $request->query->get('username');
        $options['department'] = $request->query->get('department');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:UserAccounts\User')
            ->findAllUsers($options);

        $qb2 = $em->getRepository('AppBundle:UserAccounts\User')
            ->countAllUsers($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Username','username','text',true);
        $grid->addGridHeader('Full name',null,'text',false);
        $grid->addGridHeader('Mobile',null,'text',false);
        $grid->addGridHeader('Account Status',null,'text',false);
        $grid->addGridHeader('Roles',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('portal_users_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
    

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Users',
                'gridTemplate'=>'lists/user.list.html.twig'
             ));
    }

    /**
     * @Route("/administration/user-accounts/add", name="portal_users_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {

        $this->denyAccessUnlessGranted('add',get_class($this));
        
        $form = $this->createForm(UserFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user =  $form->getData();

            $givenName = $user->getGivenNames();
            $surname = $user->getSurname();

            $fullName = $givenName.' '.$surname;

            $user->setAccountStatus('A');
            $user->setFullName($fullName);
            $user->setLoginTries(0);

            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getSurname());

            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User account successfully created');

            return $this->redirectToRoute('portal_users_list');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'portal.users/user',
                'form'=>$form->createView(),
                'title'=>'User Details'
            )

        );
    }


    /**
     * @Route("/administration/user-accounts/info/{Id}", name="portal_users_info",defaults={"Id":0})
     * @param $Id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function detailsAction($Id)
    {
        $this->denyAccessUnlessGranted('view',get_class($this));

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:UserAccounts\User')
            ->findOneBy(['id'=>$Id]);

        if(!$data instanceof User)
        {
            throw new NotFoundHttpException('User Account Not Found');
        }
        else
        {
            $info = $this->get('app.helper.info_builder');

            $status = $data->getAccountStatus();

            switch($status)
            {
                case 'I':$status='Inactive';break;
                case 'A':$status='Active';break;
                case 'B':$status='Blocked';break;
                default :$status='Unknown';
            }

            $roles = $data->getRoles();

            $userRoles = [];

            foreach ($roles as $role)
                array_push($userRoles,ucfirst(strtolower(str_replace('ROLE ','',str_replace('_',' ',$role)))));

            $info->addTextElement('Username',$data->getUsername());
            $info->addTextElement('Full name',$data->getFullName());
            $info->addTextElement('Email',$data->getEmail());
            $info->addTextElement('Mobile',$data->getMobilePhone());
            $info->addTextElement('Roles',implode(', ',$userRoles));
            $info->addTextElement('Account Status',$status);
            $info->addTextElement('Login Tries',$data->getLoginTries());

            if($data->getAccountStatus()=='A')
                $info->setLink('Block Account','user_account_block','block-user',$Id);
            else
                $info->setLink('Activate Account','user_account_un_block','activate-user',$Id);

            $info->setLink('Assign Roles','user_assign_role','module',$Id);
            $info->setLink('Reset Password','user_password_reset','password',$Id);
            $info->setLink('View activity logs','activity_log_list','history',$Id);

            $info->setPath('portal_users_info');

            //Render the output
            return $this->render(
                'main/app.info.html.twig',array(
                'info'=>$info,
                'title'=>'User Account Details',
                'infoTemplate'=>'base'
            ));
        }


    }

    /**
     * @Route("/administration/user-accounts/edit/{id}", name="portal_users_edit")
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Student $student
     * @internal param Course $course
     * @internal param School $school
     */
    public function editAction(Request $request,User $user)
    {
        $this->denyAccessUnlessGranted('edit',get_class($this));

        $form = $this->createForm(UserFormType::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $user = $form->getData();

            $givenName = $user->getGivenNames();
            $surname = $user->getSurname();
            $fullName = $givenName.' '.$surname;
            $user->setFullName($fullName);
            $em = $this->getDoctrine()->getManager();
            try
            {
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'User account successfully updated!');
            }
            catch(Exception $e)
            {
                $this->addFlash('error','Error code'.$e->getPrevious()->getCode());

            }

            return $this->redirectToRoute('portal_users_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'portal.users/user.edit',
                'form'=>$form->createView(),
                'title'=>'User Details'
            )

        );
    }

    /**
     * @Route("/administration/user-accounts/delete/{userId}", name="staff_delete")
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     */
    public function deleteAction($userId)
    {
        $this->denyAccessUnlessGranted('delete',get_class($this));

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:UserAccounts\User')->find($userId);

        if($user instanceof User)
        {
            try {
                $em->remove($user);
                $em->flush();
                $this->addFlash('success', 'User successfully removed !');
            }
            catch (PDOException $e)
            {
                $this->addFlash('warning', 'This user can not be deleted because there are ');
            }
        }
        else
        {
            $this->addFlash('error', 'User not found !');
        }

        
        return $this->redirectToRoute('portal_users_list');

    }


    /**
     * @Route("/administration/user-accounts/assign-role/{Id}", name="user_assign_role",defaults={"Id":0})
     * @param Request $request
     * @param $Id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newUserRoleAction(Request $request,$Id)
    {

        $this->denyAccessUnlessGranted('add',get_class($this));

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:UserAccounts\User')
            ->findOneBy(['id'=>$Id]);

        $form = $this->createForm(UserRoleFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $roleAssignment =  $form->getData();

            $roles = $roleAssignment->getRole();

            //Delete all programs under this given department before adding new departments
            $em->getRepository('AppBundle:UserAccounts\User')
                ->deleteUserRole($Id);

            foreach ($roles as $role)
            {
                $em->getRepository('AppBundle:UserAccounts\User')
                    ->recordUserRole($role,$Id);
            }

            $user->setLastActivity(new \DateTime('now'));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User successfully assigned role(s)');

            return $this->redirectToRoute('portal_users_info',['Id'=>$Id]);
        }
        else
        {
            $this->addFlash('info',sprintf('You are assigning roles to %s',$user->getUsername()));
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'portal.users/user.role',
                'form'=>$form->createView(),
                'title'=>'User Role Assignment Details'
            )

        );
    }



    /**
     * @Route("/administration/update-account-status/block/{Id}", name="user_account_block",defaults={"Id":0})
     * @param $Id
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function blockAccountAction($Id)
    {
        $class = get_class($this);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:UserAccounts\User')->find($Id);

        //$this->denyAccessUnlessGranted('decline',$class);
        $action = 'blocked';
        $status = 'B';

        $accountStatus =  $user->getAccountStatus();

        if($user instanceof User  && $accountStatus != 'I')
        {
            $user->setAccountStatus($status);

            $em->flush();

            $this->addFlash('success', sprintf('User account successfully %s !',$action));
        }
        else if($accountStatus == 'I')
        {
            $this->addFlash('warning', 'Inactive user account status can not be modified !');
        }
        else
        {
            $this->addFlash('error', 'User account not found !');
        }

        return $this->redirectToRoute('portal_users_info',['Id'=>$Id]);
    }


    /**
     * @Route("/administration/update-account-status/un-block/{Id}", name="user_account_un_block",defaults={"Id":0})
     * @param $Id
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function unBlockAccountAction($Id)
    {
        $class = get_class($this);

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:UserAccounts\User')->find($Id);

       // $this->denyAccessUnlessGranted('approve',$class);

        $action = 'un-blocked';
        $status = 'A';

        $accountStatus =  $user->getAccountStatus();

        if($user instanceof User  && $accountStatus != 'I')
        {
            $user->setAccountStatus($status);

            if($status=='A')
            {
                $user->setLoginTries(0);
            }

            $em->flush();

            $this->addFlash('success', sprintf('User account successfully %s !',$action));
        }
        else if($accountStatus == 'I')
        {
            $this->addFlash('warning', 'Inactive user account status can not be modified !');
        }
        else
        {
            $this->addFlash('error', 'User account not found !');
        }

        return $this->redirectToRoute('portal_users_info',['Id'=>$Id]);
    }



    /**
     * @Route("/administration/user-accounts/reset-password/{id}", name="user_password_reset",defaults={"id":0})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(Request $request,User $user)
    {
        $this->denyAccessUnlessGranted('edit',get_class($this));

        $form = $this->createForm(ResetPasswordForm::class,$user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $encoder = $this->get('security.password_encoder');
            $user->setPassword($encoder->encodePassword($user,$user->getPlainPassword()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Account password successfully updated!');

            return $this->redirectToRoute('portal_users_list');
        }
        else
        {

            $this->addFlash('info',sprintf('You are assigning roles to %s',$user->getUsername()));
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'portal.users/reset.password',
                'form'=>$form->createView(),
                'title'=>'User account password reset'
            )

        );
    }

    /**
     * @Route("/administration/activity-log/{id}", name="activity_log_list",defaults={"id":0})
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activityLogAction(Request $request,$id)
    {
        $class = get_class($this);


        $this->denyAccessUnlessGranted('view',$class);
        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['userId'] = $id;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:UserAccounts\User')
            ->findOneBy(['id'=>$options['userId']]);


        if(!$user instanceof User)
        {
            throw new NotFoundHttpException('User Account Not Found');
        }

        $this->addFlash('info',sprintf('You are viewing activity logs for %s',$user->getUsername()));

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $qb1 = $em->getRepository('AppBundle:UserAccounts\AuditTrail')
            ->findAllAuditTrailLogs($options);

        $qb2 = $em->getRepository('AppBundle:UserAccounts\AuditTrail')
            ->countAllAuditTrailLogs($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Date',null,'text',false);
        $grid->addGridHeader('IP Address',null,'text',false);
        $grid->addGridHeader('Activity',null,'text',false);
        $grid->addGridHeader('Entity',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('activity_log_list');
        $grid->setCurrentObject($class);
        $grid->setIgnoredButtons(['add','edit','delete']);
        $grid->setButtons();

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
            'records'=>$dataGrid,
            'grid'=>$grid,
            'title'=>'Existing Logs',
            'gridTemplate'=>'lists/activity.log.list.html.twig'
        ));
    }


    /**
     * @Route("/administration/activity-log/info/{Id}", name="activity_log_info",defaults={"Id":0})
     * @param $Id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function auditLogDetailsAction($Id)
    {
        $this->denyAccessUnlessGranted('view',get_class($this));

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:UserAccounts\AuditTrail')
            ->findOneBy(['logId'=>$Id]);

        if(!$data instanceof AuditTrail)
        {
            throw new NotFoundHttpException('User Audit Trail Log Not Found');
        }
        else
        {
            $info = $this->get('app.helper.info_builder');


            $info->addTextElement('Date/Time','');
            $info->addTextElement('Action',$data->getAction());
            $info->addTextElement('IP Address',$data->getIpAddress());
            $info->addTextElement('User Agent',$data->getUserAgent());
            $info->addTextElement('Original Data',$data->getOriginalData());
            $info->addTextElement('Final Data',$data->getFinalData());

            $info->setPath('portal_users_info');

            //Render the output
            return $this->render(
                'main/app.info.html.twig',array(
                'info'=>$info,
                'title'=>'Log Details',
                'infoTemplate'=>'base'
            ));
        }


    }
   


}