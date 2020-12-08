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

namespace MezzioOAuthDoctrine\Tests\Unit\Manager;

use MezzioOAuthDoctrine\Filter\ClientFilter;
use MezzioOAuthDoctrine\Manager\ClientManager;
use MezzioOAuthDoctrine\Model\Client;
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Grant;
use MezzioOAuthDoctrine\Model\RedirectUri;
use MezzioOAuthDoctrine\Model\Scope;
use PHPUnit\Framework\TestCase;

class ClientManagerTest extends TestCase
{
    use TestManagerTrait;

    protected string $managerClass = ClientManager::class;

    protected string $entityClass = Client::class;

    public function testRemove()
    {
        $client = $this->prophesize(ClientInterface::class)->reveal();

        $this->om->remove($client)
            ->shouldBeCalledOnce();
        $this->om->flush()
            ->shouldBeCalledOnce();

        $target = $this->createTarget();
        $target->remove($client);
    }

    public function testListWithNullCriteria()
    {
        $expected = $this->createMock(ClientInterface::class);
        $this->repository->findBy([])
            ->shouldBeCalledOnce()
            ->willReturn([$expected]);

        $result = $this->createTarget()->list(null);

        $this->assertSame([$expected], $result);
    }

    public function testListWithNoFilter()
    {
        $expected = $this->createMock(ClientInterface::class);
        $filter   = new ClientFilter();

        $this->repository->findBy([])
            ->shouldBeCalledOnce()
            ->willReturn([$expected]);

        $result = $this->createTarget()->list($filter);
        $this->assertSame([$expected], $result);
    }

    public function testListWithFilter()
    {
        $expected    = $this->createMock(ClientInterface::class);
        $grant       = new Grant('grant');
        $scope       = new Scope('scope');
        $redirectUri = new RedirectUri('http://example.com');

        $filter = new ClientFilter();
        $filter->addGrantCriteria($grant)
            ->addScopeCriteria($scope)
            ->addRedirectUriCriteria($redirectUri);

        $this->repository->findBy([
            'grants'        => [$grant],
            'scopes'        => [$scope],
            'redirect_uris' => [$redirectUri],
        ])
            ->shouldBeCalledOnce()
            ->willReturn([$expected]);

        $result = $this->createTarget()->list($filter);
        $this->assertSame([$expected], $result);
    }
}
