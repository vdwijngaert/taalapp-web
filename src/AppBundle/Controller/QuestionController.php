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
use AppBundle\Entity\Question;
use AppBundle\Form\CategoryType;
use AppBundle\Form\QuestionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class QuestionController
 * @package AppBundle\Controller
 *
 * @Route("/question")
 */
class QuestionController extends Controller {

    /**
     * @Route("/add/{id}", name="add_question", requirements={"id": "\d+"})
     * @Method("Post")
     * @param Category $category
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction(Category $category, Request $request) {
        $question = new Question();

        $form = $this->createForm( new QuestionType(), $question);
        $form->handleRequest($request);

        $question->setCategory($category);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist( $question );
            $em->flush();

            return new JsonResponse( true );
        } else {
            $validationErrors = $this->get( 'validator' )->validate( $question );
            $messages         = array();
            foreach ($validationErrors as $validationError) {
                $messages[] = $validationError->getMessage();
            }

            return new JsonResponse( new ErrorMessage( "Kon vraag niet toevoegen",
                "Volgende fouten deden zich voor: ", $messages ) );
        }
    }

    /**
     * @Route("/editForm/{id}", name="edit_form", requirements={"id": "\d+"})
     * @Template("AppBundle:Question:question.form.html.twig")
     *
     * @param Question $question
     *
     * @return array
     */
    public function editFormAction( Question $question )
    {

        $form = $this->createForm( new QuestionType(), $question );

        return array( 'questionForm' => $form->createView(), 'mode' => 'edit' );
    }

    /**
     * @Route("/edit/{id}", name="edit_question", requirements={"id": "\d+"})
     * @Method("Post")
     * @param Question $question
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function editAction( Question $question, Request $request )
    {
        $form = $this->createForm( new QuestionType(), $question );

        $form->handleRequest( $request );

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist( $question );
            $em->flush();

            return new JsonResponse( true );
        } else {
            $validationErrors = $this->get( 'validator' )->validate( $question );
            $messages         = array();
            foreach ($validationErrors as $validationError) {
                $messages[] = $validationError->getMessage();
            }

            return new JsonResponse( new ErrorMessage( "Kon vraag niet bewerken",
                "Volgende fouten deden zich voor: ", $messages ) );
        }
    }

    /**
     * @Route("/delete/{id}", name="delete_question", requirements={"id": "\d+"})
     * @Method("Get")
     * @param Question $question
     *
     * @return JsonResponse
     */
    public function deleteQuestionAction( Question $question )
    {
        try {
            if ($question->getCategory()->getUser() != $this->getUser()) {
                throw new \Exception( "Jij bent geen eigenaar van deze vraag." );
            }

            $em = $this->getDoctrine()->getManager();

            $question->setStatus(0);
            //$em->remove( $question );

            $em->flush();

            return new JsonResponse( true );
        } catch ( \Exception $e ) {
            return new JsonResponse( new ErrorMessage( "Kon vraag niet verwijderen",
                $e->getMessage() ) );
        }
    }
}