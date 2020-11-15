<?php

namespace AppBundle\Controller\Location;

use AppBundle\Entity\Location\Ward;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class WardController extends Controller
{

    /**
     * @Route("/wards", name="ward_list")
     * @param Request $request
     * @return Response
     *
     */
    public function listAction(Request $request)
    {
        $class = get_class($this);
        
       // $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Location\Ward')
            ->findAllWards($options);

        $qb2 = $em->getRepository('AppBundle:Location\Ward')
            ->countAllWards($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Ward Code','code','text',false);
        $grid->addGridHeader('Ward Name','wardName','text',true);
        $grid->addGridHeader('District','districtName','text',true);
        $grid->addGridHeader('Region','regionName','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('ward_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Wards',
                'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }
    

    /**
     * @Route("/api/getWards",options={"expose"=true}, name="api_get_wards")
     */
    public function getWardsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $value = $this->get('request_stack')->getCurrentRequest()->get('value');

        $data = $em->getRepository('AppBundle:Location\Ward')
            ->findWardsByDistrict($value);

        return new JsonResponse($data);
    }




}