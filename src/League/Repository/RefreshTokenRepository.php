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

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use MezzioOAuthDoctrine\Contracts\AccessTokenManagerInterface;
use MezzioOAuthDoctrine\Contracts\RefreshTokenManagerInterface;
use MezzioOAuthDoctrine\League\Entity\RefreshTokenEntity;
use MezzioOAuthDoctrine\Model\RefreshToken as RefreshTokenModel;
use MezzioOAuthDoctrine\Model\RefreshTokenInterface;

final class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    private RefreshTokenManagerInterface $refreshTokenManager;

    private AccessTokenManagerInterface $accessTokenManager;
    private string $entityClass;
    private string $modelClass;

    public function __construct(
        RefreshTokenManagerInterface $refreshTokenManager,
        AccessTokenManagerInterface $accessTokenManager,
        string $entityClass = RefreshTokenEntity::class,
        string $modelClass = RefreshTokenModel::class
    ) {
        $this->refreshTokenManager = $refreshTokenManager;
        $this->accessTokenManager  = $accessTokenManager;
        $this->entityClass         = $entityClass;
        $this->modelClass          = $modelClass;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress MoreSpecificReturnType
     */
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new $this->entityClass();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $refreshToken = $this->refreshTokenManager->find($refreshTokenEntity->getIdentifier());

        if (null !== $refreshToken) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $refreshToken = $this->buildRefreshTokenModel($refreshTokenEntity);

        $this->refreshTokenManager->save($refreshToken);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId): void
    {
        /** @var ?RefreshTokenInterface $refreshToken */
        $refreshToken = $this->refreshTokenManager->find($tokenId);

        if (null !== $refreshToken) {
            $refreshToken->revoke();
            $this->refreshTokenManager->save($refreshToken);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId): bool
    {
        /** @var ?RefreshTokenInterface $refreshToken */
        $refreshToken = $this->refreshTokenManager->find($tokenId);

        if (null === $refreshToken) {
            return true;
        }

        return $refreshToken->isRevoked();
    }

    /**
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    private function buildRefreshTokenModel(RefreshTokenEntityInterface $refreshTokenEntity): RefreshTokenInterface
    {
        $accessToken = $this->accessTokenManager->find($refreshTokenEntity->getAccessToken()->getIdentifier());

        return new $this->modelClass(
            $refreshTokenEntity->getIdentifier(),
            $refreshTokenEntity->getExpiryDateTime(),
            $accessToken
        );
    }
}
