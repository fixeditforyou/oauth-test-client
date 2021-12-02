<?php

declare(strict_types = 1);

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
class DefaultController extends AbstractController
{
    private const CLIENT_NAME = 'e4o';

    #[Route(name: 'index')]
    public function index(
        Request $request,
        ClientRegistry $clientRegistry,
    ): Response {
        $session = $request->getSession();
        /** @var AccessToken $accessToken */
        $accessToken = $session->get('access_token');

        $data = null;

        if ($accessToken) {
            $client = $clientRegistry->getClient(self::CLIENT_NAME);

            if ($accessToken->hasExpired()) {
                $refreshToken = $accessToken->getRefreshToken();
                $accessToken  = $client->refreshAccessToken($refreshToken);

                $session->set('access_token', $accessToken);
            }

            $user = $client->fetchUserFromToken($accessToken);
            $data = $user->toArray();
        }

        return $this->render('index.html.twig', [
            'userdata' => $data,
        ]);
    }

    #[Route(path: '/authorize', name: 'authorize')]
    public function authorize(
        Request $request,
        ClientRegistry $clientRegistry,
    ): Response {
        $request->getSession()->clear();

        return $clientRegistry->getClient(self::CLIENT_NAME)->redirect([], []);
    }

    /**
     * @throws IdentityProviderException
     */
    #[Route(path: '/redirect-uri', name: 'oauth_redirect_uri')]
    public function oauthRedirectUri(
        Request $request,
        ClientRegistry $clientRegistry,
    ): Response {
        $client      = $clientRegistry->getClient(self::CLIENT_NAME);
        $accessToken = $client->getAccessToken();

        $request->getSession()->set('access_token', $accessToken);

        return $this->redirectToRoute('index');
    }
}
