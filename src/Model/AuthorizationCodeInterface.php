<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Model;

use DateTimeInterface;

interface AuthorizationCodeInterface
{
    public function getIdentifier(): string;

    public function getExpiryDateTime(): DateTimeInterface;

    public function getUserIdentifier(): ?string;

    public function getClient(): Client;

    /**
     * @return iterable|Scope[]
     */
    public function getScopes(): iterable;

    public function isRevoked(): bool;

    public function revoke(): self;
}