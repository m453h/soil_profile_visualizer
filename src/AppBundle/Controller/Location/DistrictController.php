<?php

namespace AppBundle\Controller\Location;

use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DistrictController extends Controller
{

    /**
     * @Route("/administration/districts", name="council_list")
     * @param Request $request
     * @return Response
     *
     */
    public function listAction(Request $request)
    {
        $class = get_class($this);
        
        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['districtName'] = $request->query->get('districtName');
        $options['regionName'] = $request->query->get('regionName');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Location\District')
            ->findAllDistricts($options);

        $qb2 = $em->getRepository('AppBundle:Location\District')
            ->countAllDistricts($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('District Code','code','text',false);
        $grid->addGridHeader('District Name','districtName','text',true);
        $grid->addGridHeader('Region Name','regionName','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('council_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Councils',
                'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }


    /**
     * @Route("/api/getDistricts",options={"expose"=true}, name="api_get_districts")
     */
    public function getDistrictsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $value = $this->get('request_stack')->getCurrentRequest()->get('value');

        $data = $em->getRepository('AppBundle:Location\District')
            ->findDistrictsByRegion($value);

        return new JsonResponse($data);
    }

   
    
}