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

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use MezzioOAuthDoctrine\Contracts\AuthorizationCodeManagerInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Contracts\ScopeConverterInterface;
use MezzioOAuthDoctrine\League\Entity\AuthCodeEntity;
use MezzioOAuthDoctrine\Model\AuthorizationCode;
use MezzioOAuthDoctrine\Model\AuthorizationCodeInterface;

final class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    private AuthorizationCodeManagerInterface $authCodeManager;
    private ClientManagerInterface $clientManager;
    private ScopeConverterInterface $scopeConverter;

    public function __construct(
        AuthorizationCodeManagerInterface $authCodeManager,
        ClientManagerInterface $clientManager,
        ScopeConverterInterface $scopeConverter
    ) {
        $this->authCodeManager = $authCodeManager;
        $this->clientManager   = $clientManager;
        $this->scopeConverter  = $scopeConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCodeEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $authCode = $this->authCodeManager->find($authCodeEntity->getIdentifier());

        if (null !== $authCode) {
            throw UniqueTokenIdentifierConstraintViolationException::create();
        }

        $authCode = $this->buildAuthCode($authCodeEntity);
        $this->authCodeManager->save($authCode);
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId): void
    {
        /** @var ?AuthorizationCodeInterface $authCode */
        $authCode = $this->authCodeManager->find($codeId);

        if (null !== $authCode) {
            $authCode->revoke();
            $this->authCodeManager->save($authCode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        /** @var ?AuthorizationCodeInterface $authCode */
        $authCode = $this->authCodeManager->find($codeId);

        if (null === $authCode) {
            return true;
        }

        return $authCode->isRevoked();
    }

    /**
     * @psalm-suppress PossiblyInvalidArgument
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress ArgumentTypeCoercion
     */
    private function buildAuthCode(AuthCodeEntityInterface $authCodeEntity): AuthorizationCodeInterface
    {
        $client = $this->clientManager->find($authCodeEntity->getClient()->getIdentifier());

        return new AuthorizationCode(
            $authCodeEntity->getIdentifier(),
            $authCodeEntity->getExpiryDateTime(),
            $client,
            $authCodeEntity->getUserIdentifier(),
            $this->scopeConverter->toDomainArray($authCodeEntity->getScopes())
        );
    }
}
