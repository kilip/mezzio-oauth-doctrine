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

class AccessToken implements AccessTokenInterface
{
    private string $identifier;

    private DateTimeInterface $expiry;

    private ?string $userIdentifier;

    private Client $client;

    /** @var iterable|Scope[] */
    private iterable $scopes = [];

    private bool $revoked = false;

    public function __construct(
        string $identifier,
        DateTimeInterface $expiry,
        Client $client,
        ?string $userIdentifier,
        iterable $scopes
    ) {
        $this->identifier     = $identifier;
        $this->expiry         = $expiry;
        $this->client         = $client;
        $this->userIdentifier = $userIdentifier;
        $this->scopes         = $scopes;
    }

    public function __toString(): string
    {
        return $this->getIdentifier();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getExpiry(): DateTimeInterface
    {
        return $this->expiry;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return iterable|Scope[]
     */
    public function getScopes(): iterable
    {
        return $this->scopes;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): self
    {
        $this->revoked = true;

        return $this;
    }
}
