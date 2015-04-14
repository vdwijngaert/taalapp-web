<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("/icon")
 */
class IconController extends Controller
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {

    }

    /**
     * @Route("/searchByName/{searchTerm}")
     * @param $searchTerm
     *
     * @return JsonResponse
     */
    public function searchByName($searchTerm) {
        return new JsonResponse($this->getDoctrine()->getRepository('AppBundle:Icon')->searchByName($searchTerm));
    }
}
