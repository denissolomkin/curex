<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {

        $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Payment');

        return $this->render('default/index.html.twig', [
                'payments' => $repository->getUnsuccessfulEuroPayments(),
                'statistics' => $repository->getWeeklySuccessStatistics()
            ]
        );
    }
    
}
