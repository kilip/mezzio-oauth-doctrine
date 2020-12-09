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

namespace MezzioOAuthDoctrine\Tests\Unit\League\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Contracts\ScopeConverterInterface;
use MezzioOAuthDoctrine\Contracts\ScopeManagerInterface;
use MezzioOAuthDoctrine\League\Repository\ScopeRepository;
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Scope;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ScopeRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var ScopeManagerInterface|ObjectProphecy */
    private $scopeManager;
    /** @var ClientManagerInterface|ObjectProphecy */
    private $clientManager;
    /** @var ScopeConverterInterface|ObjectProphecy */
    private $scopeConverter;

    protected function setUp(): void
    {
        $this->scopeManager   = $this->prophesize(ScopeManagerInterface::class);
        $this->clientManager  = $this->prophesize(ClientManagerInterface::class);
        $this->scopeConverter = $this->prophesize(ScopeConverterInterface::class);
    }

    protected function getTarget(): ScopeRepository
    {
        return new ScopeRepository(
            $this->scopeManager->reveal(),
            $this->clientManager->reveal(),
            $this->scopeConverter->reveal()
        );
    }

    public function testGetScopeEntityReturnsNullWhenScopeNotExists()
    {
        $this->scopeManager->find('id')
            ->shouldBeCalled()
            ->willReturn(null);

        $this->assertNull($this->getTarget()->getScopeEntityByIdentifier('id'));
    }

    public function testGetScopeEntitySuccessfully()
    {
        $scope  = $this->prophesize(Scope::class)->reveal();
        $entity = $this->prophesize(ScopeEntityInterface::class)->reveal();

        $this->scopeManager->find('id')
            ->shouldBeCalled()
            ->willReturn($scope);
        $this->scopeConverter->toLeague($scope)
            ->shouldBeCalled()
            ->willReturn($entity);

        $this->assertSame(
            $entity,
            $this->getTarget()->getScopeEntityByIdentifier('id')
        );
    }

    public function testFinalizeScopesFromClientRequestedScopes()
    {
        $client       = $this->prophesize(ClientInterface::class);
        $scope        = $this->prophesize(Scope::class);
        $scopeEntity  = $this->prophesize(ScopeEntityInterface::class)->reveal();
        $clientEntity = $this->prophesize(ClientEntityInterface::class);

        $clientEntity->getIdentifier()->shouldBeCalled()->willReturn('client-id');

        $client->getScopes()->shouldBeCalled()->willReturn([]);
        $this->scopeConverter
            ->toDomainArray([$scopeEntity])
            ->shouldBeCalled()
            ->willReturn([$scope]);
        $this->assertFinalizeScopes(
            $scopeEntity,
            $scope->reveal(),
            $client->reveal(),
            $clientEntity->reveal()
        );
    }

    public function testFinalizeScopesFromClientScopes()
    {
        $client       = $this->prophesize(ClientInterface::class);
        $scope        = $this->prophesize(Scope::class);
        $scopeEntity  = $this->prophesize(ScopeEntityInterface::class)->reveal();
        $clientEntity = $this->prophesize(ClientEntityInterface::class);

        $clientEntity->getIdentifier()->shouldBeCalled()->willReturn('client-id');

        $client->getScopes()->shouldBeCalled()->willReturn([$scope]);
        $this->scopeConverter
            ->toDomainArray([$scopeEntity])
            ->shouldBeCalled()
            ->willReturn([]);
        $this->assertFinalizeScopes(
            $scopeEntity,
            $scope->reveal(),
            $client->reveal(),
            $clientEntity->reveal()
        );
    }

    public function testFinalizeScopesFromRequestedScopes()
    {
        $client       = $this->prophesize(ClientInterface::class);
        $scope        = $this->prophesize(Scope::class);
        $scopeEntity  = $this->prophesize(ScopeEntityInterface::class)->reveal();
        $clientEntity = $this->prophesize(ClientEntityInterface::class);

        $clientEntity->getIdentifier()->shouldBeCalled()->willReturn('client-id');

        $client->getScopes()->shouldBeCalled()->willReturn([$scope]);
        $this->scopeConverter
            ->toDomainArray([$scopeEntity])
            ->shouldBeCalled()
            ->willReturn([$scope]);

        $scope->__toString()->willReturn('scope');

        $this->assertFinalizeScopes(
            $scopeEntity,
            $scope->reveal(),
            $client->reveal(),
            $clientEntity->reveal()
        );
    }

    private function assertFinalizeScopes(
        ScopeEntityInterface $scopeEntity,
        Scope $scope,
        ClientInterface $client,
        ClientEntityInterface $clientEntity
    ) {
        $this->clientManager
            ->find('client-id')
            ->willReturn($client);
        $this->scopeManager
            ->resolve(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn([$scope]);
        $this->scopeConverter
            ->toLeagueArray([$scope])
            ->willReturn([$scopeEntity]);
        $result = $this->getTarget()
            ->finalizeScopes(
                [$scopeEntity],
                'grant',
                $clientEntity,
                'user-id'
            );

        $this->assertSame(
            [$scopeEntity],
            $result
        );
    }
}
