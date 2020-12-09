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

namespace MezzioOAuthDoctrine\Converter;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use MezzioOAuthDoctrine\Contracts\ScopeConverterInterface;
use MezzioOAuthDoctrine\League\Entity\ScopeEntity;
use MezzioOAuthDoctrine\Model\Scope as ScopeModel;

use function array_map;

final class ScopeConverter implements ScopeConverterInterface
{
    private string $modelClass;

    public function __construct(string $modelClass=ScopeModel::class)
    {
        $this->modelClass = $modelClass;
    }

    public function toDomain(ScopeEntityInterface $scope): ScopeModel
    {
        return new $this->modelClass($scope->getIdentifier());
    }

    /**
     * {@inheritDoc}
     */
    public function toDomainArray(array $scopes): iterable
    {
        return array_map(function (ScopeEntityInterface $scope): ScopeModel {
            return $this->toDomain($scope);
        }, $scopes);
    }

    public function toLeague(ScopeModel $scope): ScopeEntityInterface
    {
        $entity = new ScopeEntity();
        $entity->setIdentifier($scope->__toString());

        return $entity;
    }

    public function toLeagueArray(array $scopes): iterable
    {
        return array_map(function (ScopeModel $scope): ScopeEntityInterface {
            return $this->toLeague($scope);
        }, $scopes);
    }
}
