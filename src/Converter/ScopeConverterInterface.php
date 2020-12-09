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
use MezzioOAuthDoctrine\Model\Scope;

interface ScopeConverterInterface
{
    /**
     * @param array $scopes
     * @return iterable|ScopeEntityInterface[]
     */
    public function toDomainArray(array $scopes): iterable;

    public function toLeague(Scope $scope): ScopeEntityInterface;

    /**
     * @param iterable|ScopeEntityInterface[] $scopes
     * @return array|iterable|ScopeEntityInterface[]
     */
    public function toLeagueArray(iterable $scopes): iterable;
}
