<?php

declare(strict_types = 1);

namespace App\OAuth\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class E4oProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected string $serverUrl;
    protected string $serverAuthorizePath;
    protected string $serverTokenPath;
    protected string $serverProfilePath;

    public function getBaseAuthorizationUrl(): string
    {
        return "$this->serverUrl/$this->serverAuthorizePath";
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return "$this->serverUrl/$this->serverTokenPath";
    }

    protected function getDefaultScopes(): array
    {
        return [
            'user:read:profile',
        ];
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return "$this->serverUrl/$this->serverProfilePath";
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
    }

    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new GenericResourceOwner(
            $response,
            $response['email']
                ?? throw new \InvalidArgumentException('Response missing user e-mail')
        );
    }

    protected function getAllowedClientOptions(array $options): array
    {
        $options = parent::getAllowedClientOptions($options);
        $data    = parse_url($this->serverUrl);

        if (str_ends_with($data['host'], 'localhost')) {
            // THIS MIGHT BE DANGEROUS! Use for testing with self-signed certificates only.
            $options[] = 'verify';
        }

        return $options;
    }
}
