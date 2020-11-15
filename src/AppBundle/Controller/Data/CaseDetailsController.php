<?php

namespace AppBundle\Controller\Data;

use AppBundle\Entity\Configuration\Country;
use AppBundle\Entity\Data\CaseDetail;
use AppBundle\Entity\Data\CaseFolder;
use AppBundle\Form\Data\CaseDetailFormType;
use AppBundle\Form\Data\CaseFolderFormType;
use AppBundle\Helpers\GridBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CaseDetailsController extends Controller
{

    /**
     * @Route("/administration/case-detail-list/{caseFolderId}", name="case_detail_list",defaults={"caseFolderId":0})
     * @param Request $request
     * @param $caseFolderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request,$caseFolderId)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['caseFolderId'] = $caseFolderId;

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Data\CaseDetail')
            ->findAllCaseDetails($options);

        $qb2 = $em->getRepository('AppBundle:Data\CaseDetail')
            ->countAllCaseDetails($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $records = new Pagerfanta($adapter);
        $records->setMaxPerPage($maxPerPage);
        $records->setCurrentPage($page);
        $records->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Region','regionName','text',false);
        $grid->addGridHeader('District','districtName','text',false);
        $grid->addGridHeader('Ward','wardName','text',false);
        $grid->addGridHeader('Age',null,'text',false);
        $grid->addGridHeader('Status',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('case_detail_list');
        $grid->setParentValue($caseFolderId);
        $grid->setCurrentObject($class);
        $grid->setButtons();
    

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$records,
                'grid'=>$grid,
                'title'=>'List of case detail(s)',
                'gridTemplate'=>'lists/case.details.list.html.twig'
             ));
    }

    /**
     * @Route("/administration/case-detail-list/{caseFolderId}/add", name="case_detail_add")
     * @param Request $request
     * @param $caseFolderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request,$caseFolderId)
    {

        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(CaseDetailFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $caseFolderDetails = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $caseFolder = $em->getRepository('AppBundle:Data\CaseFolder')->find($caseFolderId);
            $caseFolderDetails->setCaseFolder($caseFolder);
            $em->persist($caseFolderDetails);
            $em->flush();

            $this->addFlash('success', 'Case detail(s) successfully created');

            return $this->redirectToRoute('case_detail_list',['caseFolderId'=>$caseFolderId]);
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'data/case.detail',
                'form'=>$form->createView(),
                'title'=>'Case Details'
            )

        );
    }

    /**
     * @Route("/administration/case-detail-list/{caseFolderId}/edit/{caseId}", name="case_detail_edit")
     * @param Request $request
     * @param CaseDetail $caseDetail
     * @param $caseFolderId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request,CaseDetail $caseDetail,$caseFolderId)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CaseDetailFormType::class,$caseDetail);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $caseDetail = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($caseDetail);
            $em->flush();

            $this->addFlash('success', 'Case detail(s) successfully updated!');

            return $this->redirectToRoute('case_detail_list',['caseFolderId'=>$caseFolderId]);

        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'data/case.detail',
                'form'=>$form->createView(),
                'title'=>'Case Details'
            )

        );
    }

    /**
     * @Route("/administration/case-detail-list/{caseFolderId}/delete/{caseId}", name="case_detail_delete")
     * @param $caseId
     * @param $caseFolderId
     * @return \Symfony\Component\HttpFoundation\Response* @internal param Request $request
     */
    public function deleteAction($caseId,$caseFolderId)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $case = $em->getRepository('AppBundle:Data\CaseDetail')->find($caseId);

        if($case instanceof CaseDetail)
        {
            $em->remove($case);
            $em->flush();
            $this->addFlash('success', 'Case successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Case not found !');
        }


        return $this->redirectToRoute('case_detail_list',['caseFolderId'=>$caseFolderId]);

    }
    
}