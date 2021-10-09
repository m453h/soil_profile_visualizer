<?php

namespace AppBundle\Controller\SpatialAPI;

use AppBundle\Entity\Location\SavedLocation;
use AppBundle\Entity\UserAccounts\User;
use Ddeboer\DataImport\Reader\CsvReader;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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


    /**
     * @Route("/spatialAPI/mobile-info-view",name="mobile_info_view")
     * @param Request $request
     * @return Response
     */
    public function getMobileInfoView(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $latitude = $request->get('latitude',"-6.3690");
        $longitude = $request->get('longitude',"34.8888");

        $properties = $em->getRepository('AppBundle:Configuration\SoilType')
            ->reverseGeocodeSoilProperty($latitude,$longitude);

        $properties = array_merge($properties,  $em->getRepository('AppBundle:Configuration\SoilType')
            ->reverseGeocode($latitude,$longitude));

        $crops = $em->getRepository('AppBundle:Data\CropsInRegion')
            ->getTopCropsInRegion($properties['region_code']);

        $properties['recommended_crops'] = $crops;
        //Render the output
        return $this->render(
            'main/api.info.view.html.twig',[
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'properties'=>$properties
        ]);
    }

    /**
     * @Route("/spatialAPI/mobile-places-view",name="mobile_places_view")
     * @param Request $request
     * @return Response
     */
    public function getMobilePlacesView(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $latitude = $request->get('latitude',"-6.3690");
        $longitude = $request->get('longitude',"34.8888");

        $records = $em->getRepository('AppBundle:Location\SavedLocation')
            ->getAllSavedPlaces();


        return $this->render(
            'main/api.places.view.html.twig',[
            'latitude'=>$latitude,
            'longitude'=>$longitude,
            'records'=>$records
        ]);
    }


    /**
     * @Route("/spatialAPI/save-place",name="mobile_save_place")
     * @param Request $request
     * @return Response
     */
    public function saveMobilePlace(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parser = $this->get('app.helper.array_parser');

        $content = $request->getContent();
        $data = json_decode($content, true);

        $latitude =  $parser->getFieldValue($data,'latitude');
        $longitude = $parser->getFieldValue($data,'longitude');
        $token = $parser->getFieldValue($data,'authToken');

        if($latitude == null || $longitude == null)
        {
            $data['header'] = 'Action Failed';
            $data['message'] = 'Failed adding location to my places, longitude and latitude not sent';
            return new JsonResponse($data);
        }

        $properties = $em->getRepository('AppBundle:Configuration\SoilType')
            ->reverseGeocodeSoilProperty($latitude,$longitude);

        $properties = array_merge($properties,  $em->getRepository('AppBundle:Configuration\SoilType')
            ->reverseGeocode($latitude,$longitude));

        $user = $em->getRepository('AppBundle:UserAccounts\User')
            ->findOneBy(['token' => $token]);

        if(!$user instanceof User)// || $token==null)
        {
            $data['header'] = 'Action Failed';
            $data['message'] = 'User is not logged in, please logout and login the app again';
            return new JsonResponse($data);
        }

        try {
            $location = new SavedLocation();
            $location->setWardCode($properties['ward_code']);
            $location->setSoilType($properties['soil_type']);
            $location->setLatitude($latitude);
            $location->setLongitude($longitude);
            $location->setUser($user);
            $location->setDateCreated(new \DateTimeImmutable());
            $em->persist($location);
            $em->flush();
            $data['header'] = 'Success';
            $data['message'] = 'Location has been successfully saved';
        }
        catch (UniqueConstraintViolationException $e)
        {

           // $data['header'] = 'Action Failed';
           // $data['message'] = 'Duplicate record';
        }

        $data['header'] = 'Success';
        $data['message'] = 'Location has been successfully saved';
        return new JsonResponse($data);

    }



}