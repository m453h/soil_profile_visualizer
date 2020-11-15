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


class DistrictController extends Controller
{

    /**
     * @Route("/spatialAPI/get-district-list", options={"expose"=true},name="district_spatial_data")
     * @param Request $request
     * @return Response
     *
     */
    public function listAction(Request $request)
    {
        $regionCode = $request->query->get('value');
        
        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Location\District')
            ->getDistrictGeometry(['regionCode'=>$regionCode]);

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        $response = $leafletTransformer->formatArrayToString($response);

        return new Response($response);
    }


    /**
     * @Route("/spatialAPI/get-district-spatial-report", options={"expose"=true},name="district_spatial_statistics")
     * @param Request $request
     * @return Response
     *
     */
    public function getDistrictSpatialStatisticsAction(Request $request)
    {
        $regionCode = $request->query->get('value');

        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Location\District')
            ->getDistrictSpatialStatistics(['regionCode'=>$regionCode]);

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        $response = $leafletTransformer->formatArrayToString($response);

        return new Response($response);
    }


}