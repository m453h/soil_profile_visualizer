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


class WardController extends Controller
{

    /**
     * @Route("/spatialAPI/get-ward-list", options={"expose"=true},name="ward_spatial_data")
     * @param Request $request
     * @return Response
     *
     */
    public function listAction(Request $request)
    {
        $constituencyCode = $request->query->get('value');

        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Location\Ward')
            ->getWardGeometry(['districtCode'=>$constituencyCode]);

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        $response = $leafletTransformer->formatArrayToString($response);

        return new Response($response);
    }


    /**
     * @Route("/spatialAPI/get-ward-spatial-report", options={"expose"=true},name="ward_spatial_statistics")
     * @param Request $request
     * @return Response
     *
     */
    public function getWardSpatialStatisticsAction(Request $request)
    {
        $districtCode = $request->query->get('value');

        $em = $this->getDoctrine()->getManager();

        $response = $em->getRepository('AppBundle:Location\Ward')
            ->getWardSpatialStatistics(['districtCode'=>$districtCode]);

        $leafletTransformer = $this->get('app.helper.leaflet_data_transformer');

        $response = $leafletTransformer->formatArrayToString($response);

        return new Response($response);
    }


}