<?php

namespace AppBundle\Controller\Report;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ResultsReportController extends Controller
{

    /**
     * @Route("/administration/cases-visualization", name="results_spatial_visualization")
     */
    public function homepageAction()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $template = 'main/app.dashboard.html.twig';

        return $this->render(
            $template,
            []
        );

    }


}