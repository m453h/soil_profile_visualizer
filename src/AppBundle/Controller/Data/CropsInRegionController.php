<?php

namespace AppBundle\Controller\Data;

use AppBundle\Entity\Data\CropsInRegion;
use AppBundle\Form\Data\CropsInRegionFormType;
use AppBundle\Form\Data\CropsInRegionUploadFormType;
use AppBundle\Helpers\GridBuilder;
use Ddeboer\DataImport\Reader\CsvReader;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CropsInRegionController extends Controller
{

    /**
     * @Route("/administration/crops-in-region/{regionCode}", defaults={"regionCode" = null}, name="crops_in_region_list")
     * @param Request $request
     * @param $regionCode
     * @return Response
     *
     */
    public function listAction(Request $request,$regionCode)
    {

        $class =  get_class($this);
        $this->denyAccessUnlessGranted('view',$class);

        $page = $request->query->get('page',1);
        $em = $this->getDoctrine()->getManager();

        $region = $em->getRepository('AppBundle:Location\Region')
            ->find($regionCode);

        if ($region == null)
            throw $this->createNotFoundException('Region not found');

        $this->addFlash('info',sprintf('You can add or edit crops data in %s region',$region->getRegionName()));

        $options['sortBy'] = $request->query->get('sortBy');
        $options['sortType'] = $request->query->get('sortType');
        $options['regionCode'] = $regionCode;

        $maxPerPage = $this->getParameter('grid_per_page_limit');

        $em = $this->getDoctrine()->getManager();

        $qb1 = $em->getRepository('AppBundle:Data\CropsInRegion')
            ->findAllCropsInRegion($options);

        $qb2 = $em->getRepository('AppBundle:Data\CropsInRegion')
            ->countAllCropsInRegion($qb1);

        $adapter =new DoctrineDbalAdapter($qb1,$qb2);
        $records = new Pagerfanta($adapter);
        $records->setMaxPerPage($maxPerPage);
        $records->setCurrentPage($page);
        $records->getCurrentPageResults();

        //Configure the grid
        $grid = $this->get('app.helper.grid_builder');
        $grid->addGridHeader('S/N',null,'index');
        $grid->addGridHeader('Crop Name',null,'text',false);
        $grid->addGridHeader('Crop Category',null,'text',false);
        $grid->addGridHeader('Production Value',null,'text',false);
        $grid->addGridHeader('Harvested Area',null,'text',false);
        $grid->addGridHeader('Record Year',null,'text',false);
        $grid->addGridHeader('Actions',null,'action');
        $grid->setStartIndex($page,$maxPerPage);
        $grid->setPath('crops_in_region_list');
        $grid->setParentValue($regionCode);
        $grid->setCurrentObject($class);
        $grid->setButtons();
    

        //Render the output
        return $this->render(
            'main/app.list.html.twig',array(
                'records'=>$records,
                'grid'=>$grid,
                'title'=>'List of Crops Data in Region',
                'gridTemplate'=>'lists/crops.in.data.list.html.twig'
             ));
    }

    /**
     * @Route("/administration/crops-in-region/{regionCode}/add", name="crops_in_region_add")
     * @param Request $request
     * @param $regionCode
     * @return Response
     */
    public function newAction(Request $request,$regionCode)
    {

        $class =  get_class($this);
        $this->denyAccessUnlessGranted('add',$class);

        $form = $this->createForm(CropsInRegionFormType::class);

        // only handles data on POST
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $cropsInRegion = $form->getData();
            
            $region = $this->getRegion($regionCode);
            
            $cropsInRegion->setRegion($region);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($cropsInRegion);
            $em->flush();

            $this->addFlash('success', 'Crop in region data successfully created');

            return $this->redirectToRoute('crops_in_region_list',['regionCode'=>$regionCode]);
        }


        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'data/crops.in.region',
                'form'=>$form->createView(),
                'title'=>'Crop in region details'
            )
        );
    }
    
    /**
     * @Route("/administration/crops-in-region/{regionCode}/edit/{recordId}", name="crops_in_region_edit")
     * @param Request $request
     * @param CropsInRegion $cropsInRegion
     * @param $regionCode
     * @return Response
     */
    public function editAction(Request $request,CropsInRegion $cropsInRegion,$regionCode)
    {
        $class =  get_class($this);
        $this->denyAccessUnlessGranted('edit',$class);

        $form = $this->createForm(CropsInRegionFormType::class,$cropsInRegion);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $cropsInRegion = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($cropsInRegion);
            $em->flush();

            $this->addFlash('success', 'Crop in region successfully updated!');

            return $this->redirectToRoute('crops_in_region_list',['regionCode'=>$regionCode]);
        }

        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'data/crops.in.region',
                'form'=>$form->createView(),
                'title'=>'Crop in region details'
            )

        );
    }

    /**
     * @Route("/administration/crops-in-region/{regionCode}/delete/{recordId}", name="crops_in_region_delete")
     * @param $regionCode
     * @param $recordId
     * @return Response
     * @internal param Request $request
     */
    public function deleteAction($regionCode,$recordId)
    {
        $class =  get_class($this);
        $this->denyAccessUnlessGranted('delete',$class);

        $em = $this->getDoctrine()->getManager();

        $cropsInRegion = $em->getRepository('AppBundle:Data\CropsInRegion')->find($recordId);

        if($cropsInRegion instanceof CropsInRegion)
        {
            $em->remove($cropsInRegion);
            $em->flush();
            $this->addFlash('success', 'Crop(s) in region successfully removed !');
        }
        else
        {
            $this->addFlash('error', 'Crop(s) in region not found !');
        }


        return $this->redirectToRoute('crops_in_region_list',['regionCode'=>$regionCode]);

    }

    /**
     * @Route("/course_curriculum/{curriculumId}/info/{courseCurriculumId}", name="course_curriculum_info",defaults={"curriculumId" = null,"courseCurriculumId"=null})
     * @param $courseCurriculumId
     * @return Response
     * @internal param Request $request
     * @internal param $curriculumId
     */
    public function infoAction($courseCurriculumId)
    {

        $class =  get_class($this);
        $this->denyAccessUnlessGranted('info',$class);

        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository('AppBundle:Configuration\CourseCurriculum')
            ->find($courseCurriculumId);

        $course = $data->getCourse();
        $curriculum = $data->getCurriculum();
        $weightType=$curriculum->getWeightType();
        $courseType=$data->getCourseType();
        $studyPeriod=$data->getPeriod();
        $courseAction=$data->getCourseAction();

       //dump($course->getCourseName().'---'.$course->getCourseCode());

        if(!$data)
        {
            throw new NotFoundHttpException('Results Not Found');
        }

        $info = $this->get('app.helper.info_builder');

        $info->addTextElement('Course Code',$course->getCourseCode());
        $info->addTextElement('Course Name',$course->getCourseName());
        $info->addTextElement('Weight',$data->getWeight().' '.$weightType->getTypeName());
        $info->addTextElement('CA(%)',$data->getCaWeight());
        $info->addTextElement('UE(%)',$data->getUeWeight());
        $info->addTextElement('Course Type',$courseType->getDescription());
        $info->addTextElement('Study Period',$studyPeriod->getStudyPeriod());
        $info->addTextElement('Course Action',$courseAction->getActionName());

        /*
        $info->setLink('Download PDF','course_curriculum_list','pdf','1');
        $info->setLink('View charts','course_curriculum_list','chart','1');
        $info->setLink('Print file','course_curriculum_list','print','1');
        */
        //$info->setId(1);

        //$info->setButtons("approve");
      //  $info->setButtons("decline");

        $info->setPath('course_curriculum_list');

        //Render the output
        return $this->render(
            'main/app.info.html.twig',array(
            'info'=>$info,
            'title'=>'Course Details',
            'infoTemplate'=>'base'
        ));
    }


    public function getRegion($regionCode)
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AppBundle:Location\Region')
            ->findOneBy(['regionCode' => $regionCode]);
    }

    /**
     * @Route("/administration/crops-in-region/{regionCode}/upload", name="crops_in_region_upload",defaults={"regionCode" = 0})
     * @param Request $request
     * @param $regionCode
     * @return Response
     */
    public function newUploadAction(Request $request,$regionCode)
    {
        $class =  get_class($this);
        $this->denyAccessUnlessGranted('bulk-upload',$class);

        $form = $this->createForm(CropsInRegionUploadFormType::class);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $region = $em->getRepository('AppBundle:Location\Region')
            ->find($regionCode);

        if ($region == null)
            throw $this->createNotFoundException('Region not found');

        $this->addFlash('info',sprintf('You are about to upload data for %s region',$region->getRegionName()));

        if ($form->isSubmitted() && $form->isValid())
        {
            $cropsInRegionData = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $cropsInRegionFile= (Object) $cropsInRegionData['cropsDataFile'];

            $filePath= $cropsInRegionFile->getRealpath();

            //CSV File Reader Libary (Ddeboer Implementation)
            $newFile = new SplFileObject($filePath);
            $reader = new CsvReader($newFile);
            $reader->setHeaderRowNumber(0);

            $expectedHeaders = ['crop','crop_name','harvested_area','year','id','value_of_production','crop_parent'];

            //CSV File Verification Service
            $verifier = $this->get('app.helper.file_format_verifier');
            $availableHeaders = $reader->getColumnHeaders();
            $status = $verifier->verifyFileFormatHeaders($expectedHeaders, $availableHeaders);
            $cropsInRegionRepository = $em->getRepository('AppBundle:Data\CropsInRegion');

            //Check if file headers are fine
            if ($status === true)
            {
                $duplicates = array();
                $invalidRecords = array();
                $counter = 3;

                //Loop to process if the verified data is ok
                if (empty($invalidRecords))
                {
                    foreach ($reader as $row)
                    {
                        try
                        {
                            $cropsInRegionRepository->recordCropsInRegion($regionCode,$row);
                        }
                        catch (UniqueConstraintViolationException $ex)
                        {
                            array_push($duplicates, $row['CODE'].' '.$row['NAME']);
                        } catch (Exception $e)
                        {
                            dump($e);

                        }
                    }

                    if (empty($duplicates))
                    {
                        $this->addFlash('success', 'Crops in region successfully created');
                    }
                    else
                    {
                        $duplicates = implode(', ', $duplicates);

                        $this->addFlash('error','Please recheck your file for duplicate records');
                    }

                }
                else
                {
                        $invalidRecords = implode(', ', $invalidRecords);

                        $this->addFlash('error', sprintf('Row Number %s is/are Not Configured Properly',
                                $invalidRecords));
                }

        }
        else
        {
            $this->addFlash('error', 'Invalid header file format has been uploaded please check.....');
        }

            return $this->redirectToRoute('crops_in_region_list',['regionCode'=>$regionCode]);
        }

        
        return $this->render(
            'main/app.form.html.twig',
            array(
                'formTemplate'=>'data/crops.in.region.upload',
                'form'=>$form->createView(),
                'title'=>'Crops in Region Bulk Upload'
            )

        );
    }
    
    
}