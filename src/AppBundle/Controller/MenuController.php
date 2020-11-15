<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Configuration\AcademicYear;
use AppBundle\Entity\Configuration\School;
use AppBundle\Form\Configuration\AcademicYearFormType;
use AppBundle\Form\Configuration\SchoolFormType;
use AppBundle\Helpers\GridBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class MenuController extends Controller
{

    /**
     * @Route("/location-report-menu", name="location_report_menu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function organisationalStructureMenuAction()
    {

        $menu = $this->get('app.helper.dashboard_grid_builder');

        $menu->setMenuItems('find','Regions','region_report_builder',
            'Click to view regions report');

        $menu->setMenuItems('find','Councils','council_report_builder',
            'Click to view regions report');

        $menu->setMenuItems('find','Constituencies','constituency_report_builder',
            'Click to view regions report');

        $menu->setMenuItems('find','Wards','wards_report_builder',
            'Click to view regions report');


        //Render the output
        return $this->render(
            'graph.menu.html.twig',array(
            'title'=>'Report | Location',
            'menu'=>$menu->getMenuItems(),
            'parent'=>'Report',
            'child'=>'Location'
        ));
    }


    /**
     * @Route("/general-configuration-menu", name="general_configuration_menu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generalConfigurationMenuAction()
    {

        $menu = $this->get('app.helper.dashboard_grid_builder');

        $menu->setMenuItems('globe_frame','Countries','country_list',
            'Click to manage countries');
        $menu->setMenuItems('country','Regions','region_list',
            'Click to manage regions');
        $menu->setMenuItems('currency','Currencies','currency_list',
            'Click to manage currencies');
        $menu->setMenuItems('globe','Nationalities','nationality_list',
            'Click to manage nationalities');
        $menu->setMenuItems('clock','Study Periods','study_period_list',
            'Click to manage study periods');
        $menu->setMenuItems('year','Academic Years','academic_year_list',
            'Click to manage academic years');
        $menu->setMenuItems('calendar-clock','Academic Year Events','academic_year_event_list',
            'Click to manage academic years events');
        $menu->setMenuItems('marital','Marital Status','marital_status_list',
            'Click to manage marital status');



        //Render the output
        return $this->render(
            'graph.menu.html.twig',array(
            'title'=>'Configuration | General Configurations',
            'menu'=>$menu->getMenuItems(),
            'parent'=>'Configuration',
            'child'=>'General Configuration'
        ));
    }


    /**
     * @Route("/programs-configuration-menu", name="programs_configuration_menu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function programsConfigurationMenuAction()
    {

        $menu = $this->get('app.helper.dashboard_grid_builder');

        $menu->setMenuItems('folder','Program Levels','program_level_list',
            'Click to manage program levels');
        $menu->setMenuItems('programs','Programs','program_list',
            'Click to manage programs');
        $menu->setMenuItems('distribution','Program Distribution','program_department_list',
            'Click to manage program distribution to departments');




        //Render the output
        return $this->render(
            'graph.menu.html.twig',array(
            'title'=>'Configuration | Programs',
            'menu'=>$menu->getMenuItems(),
            'parent'=>'Configuration',
            'child'=>'Programs'
        ));
    }


    /**
     * @Route("/curriculum-configuration-menu", name="curriculum_configuration_menu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function curriculumConfigurationMenuAction()
    {

        $menu = $this->get('app.helper.dashboard_grid_builder');

        $menu->setMenuItems('folders','Courses','course_list',
            'Click to manage courses');
        $menu->setMenuItems('calculator','Weight type','weight_type_list',
        'Click to manage weight type');
        $menu->setMenuItems('notepad','Exam type','exam_type_list',
            'Click to manage exam type');
        $menu->setMenuItems('contract','Course type','course_type_list',
            'Click to manage course type');
        $menu->setMenuItems('element-tree','Course action','course_action_list',
            'Click to manage course action');
        $menu->setMenuItems('grade','Grade criteria','grade_criteria_list',
            'Click to manage grade criteria');
        $menu->setMenuItems('presentation_chart','Delivery mode','delivery_mode_list',
            'Click to manage delivery mode');
        $menu->setMenuItems('gears','Examination process','examination_process_version_list',
            'Click to manage examination process version');
        $menu->setMenuItems('message','Result remarks','result_remark_list',
            'Click to manage result remark');
        $menu->setMenuItems('messages','Remark reason','remark_reason_list',
            'Click to manage remark reason');
        $menu->setMenuItems('scales','Action Codes','action_code_list',
            'Click to manage action codes');
        $menu->setMenuItems('hourglass','Examination Duration','duration_list',
            'Click to manage examination duration');
        $menu->setMenuItems('book','Curricula Definition','curriculum_list',
            'Click to manage curricula definition');

        //Render the output
        return $this->render(
            'graph.menu.html.twig',array(
            'title'=>'Configuration | Curricula',
            'menu'=>$menu->getMenuItems(),
            'parent'=>'Configuration',
            'child'=>'Curricula'
        ));
    }


    /**
     * @Route("/registration-configuration-menu", name="registration_configuration_menu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registrationConfigurationMenuAction()
    {

        $menu = $this->get('app.helper.dashboard_grid_builder');

        $menu->setMenuItems('book-open','Proposed form of study','proposed_form_of_study_list',
            'Click to manage proposed form of study');
        $menu->setMenuItems('marital','Personal relationship type','person_relationship_type_list',
            'Click to manage personal relationship type');
        $menu->setMenuItems('building','Insurance providers','insurance_provider_list',
            'Click to manage insurance providers');
        $menu->setMenuItems('hand_money','Fee structure','fee_structure_list',
            'Click to manage fee structure');
        $menu->setMenuItems('school_building','Entry Category','entry_category_list',
            'Click to manage entry category');
        $menu->setMenuItems('programs','Subject code','subject_code_list',
            'Click to manage subject code');
        $menu->setMenuItems('message','Enrollment Termination Reason','enrollment_termination_reason_list',
            'Click to manage enrollment termination reason');
        $menu->setMenuItems('process','Enrollment Process','enrollment_process_version_list',
            'Click to manage registration process');
        $menu->setMenuItems('levels','Education Level','education_level_list',
            'Click to manage Education Level');
        $menu->setMenuItems('moneybag_coins','Sponsor Type','sponsor_type_list',
            'Click to manage sponsor types');





        //Render the output
        return $this->render(
            'graph.menu.html.twig',array(
            'title'=>'Configuration | Programs',
            'menu'=>$menu->getMenuItems(),
            'parent'=>'Configuration',
            'child'=>'Registration'
        ));
    }



    /**
     * @Route("/basic-reports-menu", name="basic_reports_menu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function basicReportsMenuAction()
    {

        $menu = $this->get('app.helper.dashboard_grid_builder');

        $menu->setMenuItems('programs','Programs','program_report_list',
            'Click to view reports');

        $menu->setMenuItems('levels','Courses Under Curriculum','program_report_builder',
            'Click to view reports');

        $menu->setMenuItems('school_building','Student Admitted','admission_report_list',
            'Click to view reports');

        $menu->setMenuItems('school','Student Enrolled','enrollment_report_list',
            'Click to view reports');

       
        //Render the output
        return $this->render(
            'graph.menu.html.twig',array(
            'title'=>'Reports | Basic Reports',
            'menu'=>$menu->getMenuItems(),
            'parent'=>'Reports',
            'child'=>'Basic Reports'
        ));
    }


    /**
     * @Route("/visual-reports-menu", name="visual_reports_menu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function visualReportsMenuAction()
    {

        $menu = $this->get('app.helper.dashboard_grid_builder');

        $menu->setMenuItems('globe_frame','Countries','country_list',
            'Click to manage countries');
        $menu->setMenuItems('country','Regions','region_list',
            'Click to manage regions');
        $menu->setMenuItems('currency','Currencies','currency_list',
            'Click to manage currencies');
        $menu->setMenuItems('globe','Nationalities','nationality_list',
            'Click to manage nationalities');
        $menu->setMenuItems('clock','Study Periods','study_period_list',
            'Click to manage study periods');
        $menu->setMenuItems('year','Academic Years','academic_year_list',
            'Click to manage academic years');
        $menu->setMenuItems('calendar-clock','Academic Year Events','academic_year_event_list',
            'Click to manage academic years events');
        $menu->setMenuItems('marital','Marital Status','marital_status_list',
            'Click to manage marital status');



        //Render the output
        return $this->render(
            'graph.menu.html.twig',array(
            'title'=>'Configuration | General Configurations',
            'menu'=>$menu->getMenuItems(),
            'parent'=>'Configuration',
            'child'=>'General Configuration'
        ));
    }

    
}