<?php

namespace AppBundle\Controller\Location;

use AppBundle\Entity\Configuration\Country;
use AppBundle\Form\Location\RegionFormType;
use AppBundle\Helpers\GridBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CountryController extends Controller
{

    /**
     * @Route("/administration/countries", name="country_list")
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
        $options['countryName'] = $request->query->get('countryName');
        
        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Configuration\Country')
            ->findAllCountries($options);

        $qb2 = $em->getRepository('AppBundle:Configuration\Country')
            ->countAllCountries($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $records = new Pagerfanta($adapter);
        $records->setMaxPerPage($maxPerPage);
        $records->setCurrentPage($page);
        $records->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Country Name','countryName','text',true);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('country_list');
        $grid->setSecondaryPath('country_list');
        $grid->setCurrentObject($class);
        $grid->setButtons();
    

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$records,
                'grid'=>$grid,
                'title'=>'List of Countries',
                'gridTemplate'=>'lists/base.list.html.twig'
             ));
    }
    
    /**
     * @Route("/administration/countries/add", name="country_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {

        $class = get_class($this);

        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(RegionFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $country = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($country);
            $em->flush();

            $this->addFlash('success', 'Country successfully created');

            return $this->redirectToRoute('country_list');
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'location/country',
                'form'=>$form->createView(),
                'title'=>'Country Details'
            )

        );
    }
    
    /**
     * @Route("/administration/countries/edit/{countryId}", name="country_edit")
     * @param Request $request
     * @param Country $country
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request,Country $country)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(RegionFormType::class,$country);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $country = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($country);
            $em->flush();

            $this->addFlash('success', 'Country successfully updated!');

            return $this->redirectToRoute('country_list');

        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'location/country',
                'form'=>$form->createView(),
                'title'=>'Country Details'
            )

        );
    }

    /**
     * @Route("/administration/countries/delete/{countryId}", name="country_delete")
     * @param $countryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     */
    public function deleteAction($countryId)
    {
        $class = get_class($this);

        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $country = $em->getRepository('AppBundle:Configuration\Country')->find($countryId);

        if($country instanceof Country)
        {
            $em->remove($country);
            $em->flush();
            $this->addFlash('success', 'Country successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Country not found !');
        }


        return $this->redirectToRoute('country_list');

    }
    
}