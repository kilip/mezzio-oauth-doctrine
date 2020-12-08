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

namespace MezzioOAuthDoctrine\Manager;

use Doctrine\Persistence\ObjectManager;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Filter\ClientFilter;
use MezzioOAuthDoctrine\Model\ClientInterface;

class ClientManager implements ClientManagerInterface
{
    use ManagerTrait;

    public function __construct(ObjectManager $om, string $class)
    {
        $this->om    = $om;
        $this->class = $class;
    }

    public function save(ClientInterface $client): void
    {
        $this->doSave($client);
    }

    public function remove(ClientInterface $client): void
    {
        $this->doRemove($client);
    }

    public function list(?ClientFilter $clientFilter): iterable
    {
        $criteria = $this->filterToCriteria($clientFilter);

        return $this->getRepository()->findBy($criteria);
    }

    private function filterToCriteria(?ClientFilter $clientFilter): array
    {
        if (null === $clientFilter || false === $clientFilter->hasFilters()) {
            return [];
        }

        $criteria = [];

        $grants = $clientFilter->getGrants();
        if ($grants) {
            $criteria['grants'] = $grants;
        }

        $redirectUris = $clientFilter->getRedirectUris();
        if ($redirectUris) {
            $criteria['redirect_uris'] = $redirectUris;
        }

        $scopes = $clientFilter->getScopes();
        if ($scopes) {
            $criteria['scopes'] = $scopes;
        }

        return $criteria;
    }
}
