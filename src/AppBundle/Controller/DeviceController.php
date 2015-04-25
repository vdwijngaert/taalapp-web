<?php
/**
 * @author Koen Van den Wijngaert <koen@koenvandenwijngaert.be>
 * @scope taalapp
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryRepository;
use AppBundle\Entity\Device;
use AppBundle\Entity\ErrorMessage;
use AppBundle\Entity\Icon;
use AppBundle\Entity\Question;
use AppBundle\Form\CategoryType;
use AppBundle\Form\DeviceType;
use AppBundle\Form\QuestionType;
use AppBundle\Util\StringUtil;
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
 * @Route("/devices")
 */
class DeviceController extends Controller
{
    /**
     * @Route("/", name="devices")
     * @Template
     *
     * @return array
     */
    public function indexAction()
    {
        $deviceForm = $this->createForm( new DeviceType(), new Device() );

        $devices = $this->getDoctrine()->getRepository( 'AppBundle:Device' )->findByUser( $this->getUser() );

        return array( 'devices' => $devices, 'deviceForm' => $deviceForm->createView() );
    }

    /**
     * @Route("/add", name="add_device")
     * @Method("Post")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction( Request $request )
    {
        $device = new Device();

        $form = $this->createForm( new DeviceType(), $device );
        $form->handleRequest( $request );

        $currentUser = $this->getUser();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $repo = $this->getDoctrine()->getRepository('AppBundle:Device');

            $token = $repo->generateToken();

            $device->setToken($token);
            $device->setUser($currentUser);

            $em->persist( $device );
            $em->flush();

            return new JsonResponse( $device );
        } else {
            $validationErrors = $this->get( 'validator' )->validate( $device );
            $messages         = array();
            foreach ($validationErrors as $validationError) {
                $messages[] = $validationError->getMessage();
            }

            return new JsonResponse( new ErrorMessage( "Kon apparaat niet toevoegen",
                "Volgende fouten deden zich voor: ", $messages ) );
        }
    }

    /**
     * @Route("/remove/{id}", name="remove_device")
     * @param Device $device
     *
     * @return JsonResponse
     */
    public function removeAction(Device $device) {
        try {
            if ($device->getUser() != $this->getUser()) {
                throw new \Exception( "Jij bent niet de eigenaar van deze categorie." );
            }

            $em = $this->getDoctrine()->getManager();

            $em->remove( $device );

            $em->flush();

            return new JsonResponse( true );
        } catch ( \Exception $e ) {
            return new JsonResponse( new ErrorMessage( "Kon apparaat niet verwijderen",
                $e->getMessage() ) );
        }
    }

}