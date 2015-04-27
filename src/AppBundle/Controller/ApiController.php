<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Device;
use AppBundle\Entity\ErrorMessage;
use AppBundle\Entity\User;
use FOS\UserBundle\Doctrine\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("/api")
 */
class ApiController extends Controller
{
    private function getDeviceByRequest( Request $request )
    {
        $userId = $request->headers->get( 'X-Auth-User', 0 );
        $token  = $request->headers->get( 'X-Auth-Token', '' );

        $user = $this->getDoctrine()->getRepository( 'AppBundle:User' )->findOneById( $userId );

        if ($user == null) {
            throw new UnauthorizedHttpException( "U bent niet ingelogd." );
        }

        $device = $this->getDoctrine()->getRepository( 'AppBundle:Device' )->getDevice( $user, $token );

        if ($device == null) {
            throw new UnauthorizedHttpException( "U bent niet ingelogd." );
        }

        return $device;
    }

    /**
     * @Route("/ping")
     *
     * @return JsonResponse
     */
    public function pingAction( Request $request )
    {
        $device = $this->getDeviceByRequest( $request );

        return new JsonResponse( 'pong' );
    }
    /**
     * @Route("/getAll")
     *
     * @return JsonResponse
     */
    public function getAllAction( Request $request )
    {
        $device = $this->getDeviceByRequest( $request );

        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findByUser($device->getUser());
        $questions = $this->getDoctrine()->getRepository('AppBundle:Question')->findByUser($device->getUser());

        $return = new \StdClass();

        $return->categories = $categories;
        $return->questions = $questions;
        $return->date = (new \DateTime())->format('Y-m-d H:i:s');

        return new JsonResponse( $return );
    }

    /**
     * @Route("/login")
     * @Method("Post")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function loginAction( Request $request )
    {
        $username = $request->get( 'username', '' );
        $token    = strtoupper( $request->get( 'token', '' ) );

        /**
         * @var $userManager UserManager
         */
        $userManager = $this->container->get( 'fos_user.user_manager' );

        /**
         * @var $user User
         */
        $user = $userManager->findUserByUsername( $username );
        if ($user === null) {
            return new JsonResponse( new ErrorMessage( 'Kan niet koppelen', 'Deze gebruiker bestaat niet.' ) );
        }

        /**
         * @var $device Device
         */
        $device = $this->getDoctrine()->getRepository( 'AppBundle:Device' )->getDevice( $user, $token );


        if ($device === null) {
            return new JsonResponse( new ErrorMessage( 'Kan niet koppelen',
                'Je hebt een foutieve inlogcode ingevuld.' ) );
        } else {
            $userData = new \StdClass();

            $userData->userName   = $device->getUser()->getUsername();
            $userData->userId     = $device->getUser()->getId();
            $userData->token      = $token;
            $userData->deviceName = $device->getName();

            $device->setLastActive( new \DateTime() );

            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse( $userData );
        }
    }
}
