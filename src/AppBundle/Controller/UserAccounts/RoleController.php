<?php

namespace AppBundle\Controller\UserAccounts;


use AppBundle\Entity\UserAccounts\Permission;
use AppBundle\Entity\UserAccounts\Role;
use AppBundle\Entity\UserAccounts\User;
use AppBundle\Form\Accounts\RoleFormType;
use AppBundle\Form\Accounts\RolePermissionFormType;
use AppBundle\Form\Accounts\UserFormType;
use Exception;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class RoleController extends Controller
{

    /**
     * @Route("/user-defined-roles", name="defined_roles_list")
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
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $this->addFlash('warning','Editing system default roles may render system unusable');
        
        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:UserAccounts\Role')
            ->findAllRoles($options);

        $qb2 = $em->getRepository('AppBundle:UserAccounts\Role')
            ->countAllRoles($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Role Name','name','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('defined_roles_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
    

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Roles',
                'gridTemplate'=>'lists/base.list.html.twig'
             ));
    }

    /**
     * @Route("/user-defined-roles/add", name="defined_roles_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {

        $this->denyAccessUnlessGranted('add',get_class($this));
        
        $form = $this->createForm(RolePermissionFormType::class);

        $permissions = $this->get('app.helper.file_loader')
            ->loadFile($this->getParameter('permissions_file'));
        
        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data =  $form->getData();

            $role = $data->getRole();

            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $roleId = $role->getRoleId();

            $actionsSelected =$this->get('app.helper.file_loader')
                ->loadFile($this->getParameter('permissions_file'));

            foreach ($actionsSelected as $menu)
            {
                foreach ($menu as $item)
                {
                    $actionsSelected = $form[$item['key']]->getData();

                    if(!empty($actionsSelected))
                    {
                        $actionsSelected = json_encode($actionsSelected);

                        $object = $item['roleClass'];
                        $em->getRepository('AppBundle:UserAccounts\Permission')
                            ->recordPermission($object, $roleId, $actionsSelected);
                    }
                }
            }
            
            $this->addFlash('success', 'Role successfully created');
            
            return $this->redirectToRoute('defined_roles_list');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'portal.users/role',
                'permissions'=>$permissions,
                'form'=>$form->createView(),
                'isFullWidth'=>true,
                'title'=>'Role Details'
            )

        );
    }
    

    /**
     * @Route("/user-defined-roles/edit/{roleId}", name="defined_roles_edit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $this->denyAccessUnlessGranted('edit',get_class($this));

        $form = $this->createForm(RolePermissionFormType::class);

        $form->handleRequest($request);

        $permissions = $this->get('app.helper.file_loader')
            ->loadFile($this->getParameter('permissions_file'));

        if ($form->isSubmitted() && $form->isValid())
        {
            $data =  $form->getData();

            $role = $data->getRole();

            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            $roleId = $role->getRoleId();

            $actionsSelected =$this->get('app.helper.file_loader')
                ->loadFile($this->getParameter('permissions_file'));

            $em->getRepository('AppBundle:UserAccounts\Permission')
                ->clearPermissionByRoleId($roleId);

            foreach ($actionsSelected as $menu)
            {
                foreach ($menu as $item)
                {
                    $actionsSelected = $form[$item['key']]->getData();

                    if(!empty($actionsSelected))
                    {
                        $actionsSelected = json_encode($actionsSelected);

                        $object = $item['roleClass'];
                        $em->getRepository('AppBundle:UserAccounts\Permission')
                            ->recordPermission($object, $roleId, $actionsSelected);
                    }
                }
            }

            $this->addFlash('success', 'Role successfully updated');

            return $this->redirectToRoute('defined_roles_list');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'portal.users/role',
                'form'=>$form->createView(),
                'permissions'=>$permissions,
                'isFullWidth'=>true,
                'title'=>'Role Details'
            )

        );
    }

    /**
     * @Route("/user-defined-roles/delete/{roleId}", name="defined_roles_delete")
     * @param $roleId
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $courseId
     * @internal param Request $request
     */
    public function deleteAction($roleId)
    {
        $this->denyAccessUnlessGranted('delete',get_class($this));

        $em = $this->getDoctrine()->getManager();

        $role = $em->getRepository('AppBundle:UserAccounts\Role')->find($roleId);

        if($role instanceof Role)
        {
            $em->remove($role);
            $em->flush();
            $this->addFlash('success', 'Role successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Role not found !');
        }

        
        return $this->redirectToRoute('defined_roles_list');

    }



}