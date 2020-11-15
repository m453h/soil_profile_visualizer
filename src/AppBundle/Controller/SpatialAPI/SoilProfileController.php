<?php

namespace AppBundle\Controller\SpatialAPI;

use Ddeboer\DataImport\Reader\CsvReader;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class SoilProfileController extends Controller
{

    /**
     * @Route("/spatialAPI/get-soil-profile-list", options={"expose"=true},name="soil_profile_spatial_data")
     * @return Response
     *
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Location\Region')
            ->getRegionGeometry([]);

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        $response = $leafletTransformer->formatArrayToString($response);

        return new Response($response);
    }


    /**
     * @Route("/spatialAPI/get-soil-profile-spatial-report", options={"expose"=true},name="soil_profile_spatial_statistics")
     * @return Response
     *
     */
    public function getRegionSpatialStatisticsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Location\Region')
            ->getRegionSpatialStatistics([]);

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        ob_start('ob_gzhandler');

        $response = $leafletTransformer->formatArrayToString($response);

        $response = new Response($response , 200);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }

}