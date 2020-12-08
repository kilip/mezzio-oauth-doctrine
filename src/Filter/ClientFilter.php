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

namespace MezzioOAuthDoctrine\Filter;

use MezzioOAuthDoctrine\Model\Grant;
use MezzioOAuthDoctrine\Model\RedirectUri;
use MezzioOAuthDoctrine\Model\Scope;

use function array_merge;
use function count;

final class ClientFilter
{
    /** @var iterable|Grant[] */
    private iterable $grants = [];

    /** @var iterable|RedirectUri[] */
    private iterable $redirectUris = [];

    /** @var iterable|Scope[] */
    private iterable $scopes = [];

    public static function create(): self
    {
        return new self();
    }

    public function addGrantCriteria(Grant ...$grants): self
    {
        return $this->addCriteria($this->grants, ...$grants);
    }

    public function addRedirectUriCriteria(RedirectUri ...$redirectUris): self
    {
        return $this->addCriteria($this->redirectUris, ...$redirectUris);
    }

    public function addScopeCriteria(Scope ...$scopes): self
    {
        return $this->addCriteria($this->scopes, ...$scopes);
    }

    /**
     * @param iterable $field
     * @return $this
     * @psalm-suppress InvalidArgument
     */
    private function addCriteria(iterable &$field, object ...$values): self
    {
        if (0 === count($values)) {
            return $this;
        }

        $field = array_merge($field, $values);

        return $this;
    }

    /**
     * @return iterable|Grant[]
     */
    public function getGrants(): iterable
    {
        return $this->grants;
    }

    /**
     * @return iterable|RedirectUri[]
     */
    public function getRedirectUris(): iterable
    {
        return $this->redirectUris;
    }

    /**
     * @return iterable|Scope[]
     */
    public function getScopes(): iterable
    {
        return $this->scopes;
    }

    public function hasFilters(): bool
    {
        return ! empty($this->grants)
            || ! empty($this->redirectUris)
            || ! empty($this->scopes);
    }
}
