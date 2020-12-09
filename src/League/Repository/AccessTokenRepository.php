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

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use MezzioOAuthDoctrine\Contracts\AccessTokenManagerInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Contracts\ScopeConverterInterface;
use MezzioOAuthDoctrine\League\Entity\AccessTokenEntity;
use MezzioOAuthDoctrine\Model\AccessToken as AccessTokenModel;
use MezzioOAuthDoctrine\Model\AccessTokenInterface;

use function assert;
use function is_string;

final class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    private AccessTokenManagerInterface $accessTokenManager;

    private ClientManagerInterface $clientManager;

    private ScopeConverterInterface $scopeConverter;

    public function __construct(
        AccessTokenManagerInterface $accessTokenManager,
        ClientManagerInterface $clientManager,
        ScopeConverterInterface $scopeConverter
    ) {
        $this->accessTokenManager = $accessTokenManager;
        $this->clientManager      = $clientManager;
        $this->scopeConverter     = $scopeConverter;
    }

    /**
     * {@inheritDoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        assert(is_string($userIdentifier));
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        $accessToken->setUserIdentifier($userIdentifier);

        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }

        return $accessToken;
    }

    /**
     * {@inheritDoc}
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $accessToken = $this->accessTokenManager->find($accessTokenEntity->getIdentifier());

        if (null !== $accessToken) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $accessToken = $this->buildAccessTokenModel($accessTokenEntity);

        $this->accessTokenManager->save($accessToken);
    }

    /**
     * {@inheritDoc}
     */
    public function revokeAccessToken($tokenId): void
    {
        /** @var ?AccessTokenInterface $accessToken */
        $accessToken = $this->accessTokenManager->find($tokenId);

        if (null !== $accessToken) {
            $accessToken->revoke();
            $this->accessTokenManager->save($accessToken);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        /** @var ?AccessTokenInterface $accessToken */
        $accessToken = $this->accessTokenManager->find($tokenId);

        if (null === $accessToken) {
            return true;
        }

        return $accessToken->isRevoked();
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function buildAccessTokenModel(AccessTokenEntityInterface $accessTokenEntity): AccessTokenModel
    {
        $client = $this->clientManager->find($accessTokenEntity->getClient()->getIdentifier());

        $userId = $accessTokenEntity->getUserIdentifier();
        assert(is_string($userId));

        return new AccessTokenModel(
            $accessTokenEntity->getIdentifier(),
            $accessTokenEntity->getExpiryDateTime(),
            $client,
            $userId,
            $this->scopeConverter->toDomainArray($accessTokenEntity->getScopes())
        );
    }
}
