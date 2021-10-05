<?php

namespace AppBundle\Controller\Location;

use AppBundle\Entity\Configuration\SoilType;
use AppBundle\Entity\Location\Region;
use AppBundle\Form\Configuration\SoilTypeFormType;
use AppBundle\Form\Location\RegionFormType;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RegionController extends Controller
{

    /**
     * @Route("/administration/regions", name="region_list")
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
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Location\Region')
            ->findAllRegions($options);

        $qb2 = $em->getRepository('AppBundle:Location\Region')
            ->countAllRegions($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $dataGrid = new Pagerfanta($adapter);
        $dataGrid->setMaxPerPage($maxPerPage);
        $dataGrid->setCurrentPage($page);
        $dataGrid->getCurrentPageResults();
        
        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Region Code','code','text',false);
        $grid->addGridHeader('Region Name','name','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('region_list');
        $grid->setSecondaryPath('crops_in_region_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
        
        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$dataGrid,
                'grid'=>$grid,
                'title'=>'Existing Regions',
                'gridTemplate'=>'lists/base.list.html.twig'
        ));
    }

    /**
     * @Route("/administration/regions/edit/{regionCode}", name="region_edit")
     * @param Request $request
     * @param Region $region
     * @return Response
     */
    public function editAction(Request $request, Region $region)
    {
        $this->denyAccessUnlessGranted('edit', get_class($this));

        $form = $this->createForm(RegionFormType::class,$region);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $region = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($region);
            $em->flush();

            $this->addFlash('success', 'Region successfully updated!');

            return $this->redirectToRoute('region_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'location/region',
                'form'=>$form->createView(),
                'title'=>'Region Details'
            )

        );
    }


    
}