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

namespace MezzioOAuthDoctrine\Model;

use DateTimeInterface;

interface AuthorizationCodeInterface
{
    public function getIdentifier(): string;

    public function getExpiryDateTime(): DateTimeInterface;

    public function getUserIdentifier(): ?string;

    public function getClient(): ClientInterface;

    /**
     * @return iterable|Scope[]
     */
    public function getScopes(): iterable;

    public function isRevoked(): bool;

    public function revoke(): self;
}
