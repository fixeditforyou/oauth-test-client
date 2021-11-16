<?php

declare(strict_types = 1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
class DefaultController extends AbstractController
{
    #[Route(name: 'index')]
    public function index(
        Request $request,
    ): Response {
        return $this->render('index.html.twig', [
            'userdata' => $request->getSession()->get('userdata'),
        ]);
    }

    #[Route(path: '/authorize', name: 'authorize')]
    public function authorize(
        Request $request,
        ClientRegistry $clientRegistry,
    ): Response {
        $request->getSession()->clear();

        return $clientRegistry->getClient('e4o')->redirect([], []);
    }

    #[Route(path:'/redirect-uri', name: 'oauth_redirect_uri')]
    public function oauthRedirectUri(
        Request $request,
        ClientRegistry $clientRegistry,
    ): Response {
        $client = $clientRegistry->getClient('e4o');

        $user = $client->fetchUser();
        $request->getSession()->set('userdata', $user->toArray());

        return $this->redirectToRoute('index');
    }
}
