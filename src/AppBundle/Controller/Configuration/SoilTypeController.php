<?php

namespace AppBundle\Controller\Configuration;

use AppBundle\Entity\Configuration\Country;
use AppBundle\Entity\Configuration\SoilType;
use AppBundle\Form\Configuration\SoilTypeFormType;
use AppBundle\Helpers\GridBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SoilTypeController extends Controller
{

    /**
     * @Route("/soil-types", name="soil_type_list")
     * @param Request $request
     * @return Response
     *
     */
    public function listAction(Request $request)
    {
        $class =  get_class($this);

        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['name'] = $request->query->get('name');

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Configuration\SoilType')
            ->findAllSoilType($options);

        $qb2 = $em->getRepository('AppBundle:Configuration\SoilType')
            ->countAllSoilTypes($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $records = new Pagerfanta($adapter);
        $records->setMaxPerPage($maxPerPage);
        $records->setCurrentPage($page);
        $records->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Code',null,'text',true);
        $grid->addGridHeader('Name','name','text',true);
        $grid->addGridHeader('Colour',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('soil_type_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();


        $data = $em->getRepository('AppBundle:Configuration\SoilType')
            ->getData();

        foreach ($data as $item)
        {
            $soilType = new SoilType();
            $soilType->setCode($item['soil_type']);
            $soilType->setName($item['main_type']);
           // $em->persist($soilType);
           // $em->flush();
        }



        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$records,
                'grid'=>$grid,
                'title'=>'List of Soil Types',
                'gridTemplate'=>'lists/soil.type.list.html.twig'
             ));
    }
    
    /**
     * @Route("/soil-types/add", name="soil_type_add")
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('add', get_class($this));

        $form = $this->createForm(SoilTypeFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $soilType = $form->getData();
            $em = $this->getDoctrine()->getManager();
            dump($soilType);
            $em->persist($soilType);
            $em->flush();

            $this->addFlash('success', 'Soil Type successfully created');

            return $this->redirectToRoute('soil_type_list');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/soil.type',
                'form'=>$form->createView(),
                'title'=>'Soil Type Details'
            )

        );
    }
    
    /**
     * @Route("/soil-types/edit/{code}", name="soil_type_edit")
     * @param Request $request
     * @param SoilType $nationality
     * @return Response
     */
    public function editAction(Request $request, SoilType $nationality)
    {
        $this->denyAccessUnlessGranted('edit', get_class($this));

        $form = $this->createForm(SoilTypeFormType::class,$nationality);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $soilType = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($nationality);
            $em->flush();

            $this->addFlash('success', 'Soil Type successfully updated!');

            return $this->redirectToRoute('soil_type_list');
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'configuration/soil.type',
                'form'=>$form->createView(),
                'title'=>'Soil Type Details'
            )

        );
    }

    /**
     * @Route("/soil-types/delete/{code}", name="soil_type_delete")
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($code)
    {
        $this->denyAccessUnlessGranted('delete', get_class($this));

        $em = $this->getDoctrine()->getManager();

        $soilType = $em->getRepository('AppBundle:Configuration\SoilType')->find($code);

        if($soilType instanceof SoilType)
        {
            $em->remove($soilType);
            $em->flush();
            $this->addFlash('success', 'Soil Type successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Soil Type not found !');
        }

        return $this->redirectToRoute('soil_type_list');
    }
    
}