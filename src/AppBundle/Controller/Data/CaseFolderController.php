<?php

namespace AppBundle\Controller\Data;

use AppBundle\Entity\Configuration\Country;
use AppBundle\Entity\Data\CaseFolder;
use AppBundle\Form\Data\CaseFolderFormType;
use AppBundle\Helpers\GridBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CaseFolderController extends Controller
{

    /**
     * @Route("/administration/case-folder-list", name="case_folder_list")
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

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Data\CaseFolder')
            ->findAllCaseFolders($options);

        $qb2 = $em->getRepository('AppBundle:Data\CaseFolder')
            ->countAllCaseFolders($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $records = new Pagerfanta($adapter);
        $records->setMaxPerPage($maxPerPage);
        $records->setCurrentPage($page);
        $records->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Folder Open Date','folderOpenDate','text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('case_folder_list');
        $grid->setSecondaryPath('case_detail_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
    

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$records,
                'grid'=>$grid,
                'title'=>'List of case folder(s)',
                'gridTemplate'=>'lists/base.list.html.twig'
             ));
    }
    
    /**
     * @Route("/administration/case-folder-list/add", name="case_folder_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {

        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(CaseFolderFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $caseFolder = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($caseFolder);
            $em->flush();

            $this->addFlash('success', 'Case folder successfully created');

            return $this->redirectToRoute('case_folder_list');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'data/case.folder',
                'form'=>$form->createView(),
                'title'=>'Case Folder Details'
            )

        );
    }

    /**
     * @Route("/administration/case-folder-list/edit/{caseFolderId}", name="case_folder_edit")
     * @param Request $request
     * @param CaseFolder $caseFolder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request,CaseFolder $caseFolder)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CaseFolderFormType::class,$caseFolder);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $caseFolder = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($caseFolder);
            $em->flush();

            $this->addFlash('success', 'Case folder successfully updated!');

            return $this->redirectToRoute('case_folder_list');

        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'data/case.folder',
                'form'=>$form->createView(),
                'title'=>'Case Folder Details'
            )

        );
    }

    /**
     * @Route("/administration/case-folder-list/delete/{caseFolderId}", name="case_folder_delete")
     * @param $caseFolderId
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     */
    public function deleteAction($caseFolderId)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $caseFolder = $em->getRepository('AppBundle:Data\CaseFolder')->find($caseFolderId);

        if($caseFolder instanceof CaseFolder)
        {
            $em->remove($caseFolder);
            $em->flush();
            $this->addFlash('success', 'Case folder successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Case folder not found !');
        }


        return $this->redirectToRoute('case_folder_list');

    }
    
}