<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Model;

interface ClientInterface
{
    public function getIdentifier(): string;

    public function getSecret(): ?string;

    /**
     * @return iterable|RedirectUri[]
     */
    public function getRedirectUris(): iterable;

    public function setRedirectUris(RedirectUri ...$redirectUris): self;

    /**
     * @return iterable|Grant[]
     */
    public function getGrants(): iterable;

    public function setGrants(Grant ...$grants): self;

    /**
     * @return iterable|Scope[]
     */
    public function getScopes(): iterable;

    public function setScopes(Scope ...$scopes): self;

    public function isActive(): bool;

    public function setActive(bool $active): self;

    public function isConfidential(): bool;

    public function isPlainTextPkceAllowed(): bool;

    public function setAllowPlainTextPkce(bool $allowPlainTextPkce): self;
}