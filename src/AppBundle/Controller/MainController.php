<?php

namespace AppBundle\Controller;

use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class MainController extends Controller
{

    /**
     * @Route("/home", name="app_home_page")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homepageAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $template = 'main/app.main.dashboard.html.twig';

        $em = $this->getDoctrine()->getManager();
        $statistics = $em->getRepository('AppBundle:Data\CaseFolder')
            ->getStatistics();

        $view = $request->get('view');

        $miniTemplate = 'dashboard/map.view.html.twig';

        $data = [];

        $this->get('app.helper.audit_trail_logger')
            ->logUserAction('DASHBOARD\MAP','LOGIN',null,null);

        if($view=='map')
        {
            $miniTemplate = 'dashboard/map.view.html.twig';
        }
        else if($view=='graphs')
        {
            $miniTemplate = 'dashboard/graph.view.html.twig';

            $data = $em->getRepository('AppBundle:Data\CaseFolder')
                ->getDailyStatistics();

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('DASHBOARD\GRAPHS','LOGIN',null,null);
        }
        else if($view=='press-releases')
        {
            $miniTemplate = 'dashboard/press.view.html.twig';

            $page = $request->query->get('page',1);
            $options['sortBy'] = $request->query->get('sortBy');
            $options['sortType'] = $request->query->get('sortType');
            $options['type'] = 'dashboard';

            $maxPerPage = 5;

            $em = $this->getDoctrine()->getManager();

            $qb1 = $em->getRepository('AppBundle:Data\CaseFolder')
                ->findAllCaseFolders($options);

            $qb2 = $em->getRepository('AppBundle:Data\CaseFolder')
                ->countAllCaseFolders($qb1);

            $adapter =new DoctrineDbalAdapter($qb1,$qb2);
            $records = new Pagerfanta($adapter);
            $records->setMaxPerPage($maxPerPage);
            $records->setCurrentPage($page);

            $data['records']=$records->getCurrentPageResults();
            $data['pagerFantaItems']=$records;

            $this->get('app.helper.audit_trail_logger')
                ->logUserAction('DASHBOARD\PRESS_RELEASES','LOGIN',null,null);
        }


        return $this->render(
            $template,
            [
                'statistics'=>$statistics,
                'miniTemplate'=>$miniTemplate,
                'extra'=>$data,
                'view'=>$view
            ]
        );

    }







}