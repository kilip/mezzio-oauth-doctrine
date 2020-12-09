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
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Grant;
use MezzioOAuthDoctrine\Model\Scope;

interface ScopeManagerInterface
{
    public function find(string $identifier): ?Scope;

    public function save(Scope $scope): void;

    /**
     * @param iterable $scopes
     * @return array|iterable|ScopeEntityInterface[]
     */
    public function resolve(iterable $scopes, Grant $grant, ClientInterface $client, ?string $userIdentifier): iterable;
}
