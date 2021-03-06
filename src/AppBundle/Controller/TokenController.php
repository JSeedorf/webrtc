<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class TokenController extends Controller
{
    /**
     * @Route("/tokens")
     * @Method("POST")
     */
    public function newTokenAction(Request $request)
    {
        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->findOneBy(['username' => $request->getUser()]);

        if(!$user) throw $this->createNotFoundException('User existiert nicht');

        $isValid = $this->get('security.password_encoder')->isPasswordValid($user, $request->getPassword());
        if(!$isValid) throw new BadCredentialsException('Invalide Eingabe');

        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode(['exp' => time() + 86400, 'username' => $user->getUsername()]);

        return new JsonResponse(['token' => $token]);
    }
}
