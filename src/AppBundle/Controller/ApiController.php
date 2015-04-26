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

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/ping")
     *
     * @return JsonResponse
     */
    public function pingAction()
    {
        return new JsonResponse( 'pong' );
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

            $userData->userName = $device->getUser()->getUsername();
            $userData->userId   = $device->getUser()->getId();
            $userData->token    = $token;

            $device->setLastActive( new \DateTime() );

            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse( $userData );
        }
    }
}
