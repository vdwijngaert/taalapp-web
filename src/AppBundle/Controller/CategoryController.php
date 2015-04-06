<?php
/**
 * @author Koen Van den Wijngaert <koen@koenvandenwijngaert.be>
 * @scope taalapp
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 */
class CategoryController extends Controller {
    /**
     * @Route("/", name="homepage")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Category:index.html.twig');
    }
}