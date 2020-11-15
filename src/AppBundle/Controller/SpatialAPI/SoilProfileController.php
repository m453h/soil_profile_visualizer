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
     * @Route("/spatialAPI/get-soil-profile-spatial-report", options={"expose"=true},name="soil_profile_spatial_statistics")
     * @param Request $request
     * @return Response
     */
    public function getRegionSpatialStatisticsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Configuration\SoilType')
              ->getRegionSoilProfileGeometry();

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        ob_start('ob_gzhandler');

        $response = $leafletTransformer->formatArrayToString($response);

        $response = new Response($response , 200);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }

}