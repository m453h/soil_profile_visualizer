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

    /**
     * @Route("/spatialAPI/reverse-geocode", options={"expose"=true},name="api_reverse_geocode")
     * @param Request $request
     * @return Response
     */
    public function getLocationName(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');

        $response = $em->getRepository('AppBundle:Configuration\SoilType')
            ->reverseGeocode($latitude,$longitude);

        $soilProfile = $em->getRepository('AppBundle:Configuration\SoilType')
            ->reverseGeocodeSoilProperty($latitude,$longitude);

        if(isset($soilProfile['soil_type']))
            $response['soil_type'] = $soilProfile['soil_type'];

        if(isset($soilProfile['main_type']))
            $response['main_type'] = $soilProfile['main_type'];

        return new JsonResponse($response);
    }

    /**
     * @Route("/spatialAPI/mobile-map-view",name="mobile_map_view")
     * @param Request $request
     * @return Response
     */
    public function getMobileMapView(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $latitude = $request->get('latitude',"-6.3690");
        $longitude = $request->get('longitude',"34.8888");

        $properties = $em->getRepository('AppBundle:Configuration\SoilType')
            ->reverseGeocodeSoilProperty($latitude,$longitude);

        //Render the output
        return $this->render(
            'main/api.map.view.html.twig',[
                'latitude'=>$latitude,
                'longitude'=>$longitude,
                'properties'=>$properties
        ]);
    }


}