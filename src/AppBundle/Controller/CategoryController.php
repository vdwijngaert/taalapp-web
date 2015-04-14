<?php
/**
 * @author Koen Van den Wijngaert <koen@koenvandenwijngaert.be>
 * @scope taalapp
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryRepository;
use AppBundle\Entity\ErrorMessage;
use AppBundle\Entity\Icon;
use AppBundle\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CategoryController
 * @package AppBundle\Controller
 */
class CategoryController extends Controller {
    /**
     * @Route("/{id}", name="homepage", defaults={"id" = null}, requirements={"id": "\d+"})
     * @Template
     */
    public function indexAction(Category $parent = null)
    {
        /**
         * @var $repo CategoryRepository
         */
        $repo = $this->getDoctrine()->getRepository('AppBundle:Category');

        $categories = $repo->getCategories($parent, $this->getUser());

        $categoryForm = $this->createForm(new CategoryType(), new Category(), array('em' => $this->getDoctrine()->getManager()));
        return array('categoryForm' => $categoryForm->createView(), 'parent' => $parent, 'categories' => $categories);
    }


    /**
     * @Route("/loadIcons")
     *
    public function loadIcons() {
        $iconRepo = $this->getDoctrine()->getRepository('AppBundle:Icon');
        $em = $this->getDoctrine()->getManager();

        $icons = glob(__DIR__ . "/../Resources/public/images/icons/*.png");

        foreach($icons as $file) {
            $icon = new Icon($file);

            $em->persist($icon);
        }

        $em->flush();

        return new Response(sizeof($iconRepo->findAll()));
    }*/

    /**
     * @Route("/addCategory")
     * @Method("Post")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addCategoryAction(Request $request) {
        $category = new Category();
        $form = $this->createForm(new CategoryType(), $category, array('em' => $this->getDoctrine()->getManager()));

        $form->handleRequest($request);

        $category->setUser($this->getUser());

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return new JsonResponse(true);
        } else {
            $validationErrors = $this->get( 'validator' )->validate( $category );
            $messages         = array();
            foreach ($validationErrors as $validationError) {
                $messages[] = $validationError->getMessage();
            }

            return new JsonResponse( new ErrorMessage( "Kon categorie niet toevoegen",
                "Volgende fouten deden zich voor: ", $messages ) );
        }
    }
}