<?php

/*
 * This file is part of the MezzioOAuthDoctrine project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MezzioOAuthDoctrine\League\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\League\Entity\ClientEntity;
use MezzioOAuthDoctrine\Model\ClientInterface;

use function array_map;
use function assert;
use function hash_equals;
use function in_array;
use function is_array;

final class ClientRepository implements ClientRepositoryInterface
{
    private ClientManagerInterface $clientManager;

    public function __construct(ClientManagerInterface $clientManager)
    {
        $this->clientManager = $clientManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier): ?ClientEntityInterface
    {
        /** @var ?ClientInterface $client */
        $client = $this->clientManager->find($clientIdentifier);

        if (null === $client) {
            return null;
        }

        return $this->buildClientEntity($client);
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        /** @var ?ClientInterface $client */
        $client = $this->clientManager->find($clientIdentifier);

        if (null === $client) {
            return false;
        }

        if (! $client->isActive()) {
            return false;
        }

        if (! $this->isGrantSupported($client, $grantType)) {
            return false;
        }

        if (! $client->isConfidential() || hash_equals((string) $client->getSecret(), (string) $clientSecret)) {
            return true;
        }

        return false;
    }

    /**
     * @psalm-suppress MixedArgument
     * @psalm-suppress InvalidArrayAccess
     * @psalm-suppress InvalidArgument
     */
    private function buildClientEntity(ClientInterface $client): ClientEntityInterface
    {
        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($client->getIdentifier());
        $clientEntity->setRedirectUri(array_map('strval', $client->getRedirectUris()));
        $clientEntity->setConfidential($client->isConfidential());
        $clientEntity->setAllowPlainTextPkce($client->isPlainTextPkceAllowed());

        return $clientEntity;
    }

    private function isGrantSupported(ClientInterface $client, ?string $grant): bool
    {
        if (null === $grant) {
            return true;
        }

        $grants = $client->getGrants();
        assert(is_array($grants));

        if (empty($grants)) {
            return true;
        }

        return in_array($grant, $grants);
    }
}
