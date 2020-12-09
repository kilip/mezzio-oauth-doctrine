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

namespace MezzioOAuthDoctrine\Contracts;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use MezzioOAuthDoctrine\Model\Scope as ScopeModel;

interface ScopeConverterInterface
{
    /**
     * @return object|ScopeModel
     */
    public function toDomain(ScopeEntityInterface $scope): object;

    /**
     * @param array|ScopeEntityInterface[] $scopes
     * @return iterable|ScopeModel[]
     */
    public function toDomainArray(array $scopes): iterable;

    public function toLeague(ScopeModel $scope): ScopeEntityInterface;

    /**
     * @param array|ScopeModel[] $scopes
     * @return array|iterable|ScopeEntityInterface[]
     */
    public function toLeagueArray(array $scopes): iterable;
}
