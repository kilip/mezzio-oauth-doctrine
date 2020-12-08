<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Model;

use DateTimeInterface;

interface AccessTokenInterface
{
    public function getIdentifier(): string;

    public function getExpiry(): DateTimeInterface;

    public function getUserIdentifier(): ?string;

    public function getClient(): Client;

    /**
     * @return iterable|Scope[]
     */
    public function getScopes(): iterable;

    public function isRevoked(): bool;

    public function revoke(): \MezzioOAuthDoctrine\Model\AccessToken;
}