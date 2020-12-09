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
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Contracts\UserConverterInterface;
use MezzioOAuthDoctrine\Contracts\UserResolverInterface;
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Grant;

class UserRepository implements UserRepositoryInterface
{
    private ClientManagerInterface $clientManager;
    private UserConverterInterface $userConverter;
    private UserResolverInterface $userResolver;

    public function __construct(
        ClientManagerInterface $clientManager,
        UserConverterInterface $userConverter,
        UserResolverInterface $userResolver
    ) {
        $this->clientManager = $clientManager;
        $this->userConverter = $userConverter;
        $this->userResolver  = $userResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        /** @var ?ClientInterface $client */
        $client = $this->clientManager->find($clientEntity->getIdentifier());

        if (null === $client) {
            return null;
        }

        $user = $this->userResolver->resolveUser(
            $username,
            $password,
            new Grant($grantType),
            $client
        );

        if (null === $user) {
            return null;
        }

        return $this->userConverter->toLeague($user);
    }
}
