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
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Contracts\ScopeConverterInterface;
use MezzioOAuthDoctrine\Contracts\ScopeManagerInterface;
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\ClientInterface as ClientModel;
use MezzioOAuthDoctrine\Model\Grant as GrantModel;
use MezzioOAuthDoctrine\Model\Scope as ScopeModel;

use function array_map;
use function assert;
use function in_array;
use function is_array;

final class ScopeRepository implements ScopeRepositoryInterface
{
    private ScopeManagerInterface $scopeManager;
    private ClientManagerInterface $clientManager;
    private ScopeConverterInterface $scopeConverter;

    public function __construct(
        ScopeManagerInterface $scopeManager,
        ClientManagerInterface $clientManager,
        ScopeConverterInterface $scopeConverter
    ) {
        $this->scopeManager   = $scopeManager;
        $this->clientManager  = $clientManager;
        $this->scopeConverter = $scopeConverter;
    }

    /**
     * {@inheritDoc}
     */
    public function getScopeEntityByIdentifier($identifier): ?ScopeEntityInterface
    {
        $scope = $this->scopeManager->find($identifier);

        if (null === $scope) {
            return null;
        }

        return $this->scopeConverter->toLeague($scope);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @return array|iterable|ScopeEntityInterface[]
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ): iterable {
        $client = $this->clientManager->find($clientEntity->getIdentifier());

        assert($client instanceof ClientInterface);
        $requestedScopes = $this->scopeConverter->toDomainArray($scopes);
        assert(is_array($requestedScopes));
        $scopes = $this->setupScopes($client, $requestedScopes);
        $scopes = $this->scopeManager->resolve(
            $scopes,
            new GrantModel($grantType),
            $client,
            $userIdentifier
        );

        return $this->scopeConverter->toLeagueArray($scopes);
    }

    /**
     * @param array|ScopeEntityInterface[]|ScopeModel[] $requestedScopes
     * @return array|ScopeModel[]
     */
    private function setupScopes(ClientModel $client, array $requestedScopes): array
    {
        /** @var ScopeModel[] $clientScopes */
        $clientScopes = $client->getScopes();

        if (empty($clientScopes)) {
            return $requestedScopes;
        }

        if (empty($requestedScopes)) {
            return $clientScopes;
        }

        $finalizedScopes       = [];
        $clientScopesAsStrings = array_map('strval', $clientScopes);

        /** @var ScopeModel $requestedScope */
        foreach ($requestedScopes as $requestedScope) {
            $requestedScopeAsString = (string) $requestedScope;
            if (! in_array($requestedScopeAsString, $clientScopesAsStrings, true)) {
                throw OAuthServerException::invalidScope($requestedScopeAsString);
            }

            $finalizedScopes[] = $requestedScope;
        }

        return $finalizedScopes;
    }
}
