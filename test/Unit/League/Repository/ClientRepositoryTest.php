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
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\League\Repository\ClientRepository;
use MezzioOAuthDoctrine\Model\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ClientRepositoryTest extends TestCase
{
    use ProphecyTrait;

    private function getTarget(ClientManagerInterface $client): ClientRepository
    {
        return new ClientRepository($client);
    }

    public function testGetClientEntityReturnsNullWhenClientNotFound()
    {
        $clientManager = $this->prophesize(ClientManagerInterface::class);
        $clientManager->find('client-id')
            ->shouldBeCalledOnce()
            ->willReturn(null);
        $target = $this->getTarget($clientManager->reveal());

        $this->assertNull($target->getClientEntity('client-id'));
    }

    public function testGetClientEntity()
    {
        $client        = $this->prophesize(ClientInterface::class);
        $clientManager = $this->prophesize(ClientManagerInterface::class);

        $client->getIdentifier()->willReturn('client-id');
        $client->getRedirectUris()->willReturn([]);
        $client->isConfidential()->willReturn(false);
        $client->isPlainTextPkceAllowed()->willReturn(true);

        $clientManager->find('client-id')
            ->shouldBeCalledOnce()
            ->willReturn($client);

        $result = $this->getTarget($clientManager->reveal())->getClientEntity('client-id');

        $this->assertInstanceOf(ClientEntityInterface::class, $result);
    }

    public function testValidateClientReturnsNullWhenClientNotFound()
    {
        $clientManager = $this->prophesize(ClientManagerInterface::class);

        $clientManager->find('client-id')->willReturn(null);
        $this->assertValidateClient($clientManager, false);
    }

    public function testValidateClientReturnsFalseWhenClientIsActive()
    {
        $client        = $this->prophesize(ClientInterface::class);
        $clientManager = $this->prophesize(ClientManagerInterface::class);

        $client->isActive()->shouldBeCalledOnce()->willReturn(false);
        $clientManager->find('client-id')
            ->shouldBeCalledOnce()
            ->willReturn($client);

        $this->assertValidateClient($clientManager, false);
    }

    public function testValidateClientReturnFalseWhenGrantIsNotSupported()
    {
        $client        = $this->prophesize(ClientInterface::class);
        $clientManager = $this->prophesize(ClientManagerInterface::class);

        $client->isActive()->shouldBeCalledOnce()->willReturn(true);
        $client->getGrants()->shouldBeCalled()->willReturn(['foo']);
        $clientManager->find('client-id')->willReturn($client);

        $this->assertValidateClient($clientManager, false);
    }

    public function testValidateClientWithConfidential()
    {
        $client        = $this->prophesize(ClientInterface::class);
        $clientManager = $this->prophesize(ClientManagerInterface::class);

        $client->isActive()->shouldBeCalledOnce()->willReturn(true);
        $client->getGrants()->shouldBeCalled()->willReturn(['grant']);
        $client->isConfidential()->shouldBeCalled()->willReturn(false);

        $clientManager->find('client-id')->willReturn($client);

        $this->assertValidateClient($clientManager, true);
    }

    public function testValidateClientWithSecret()
    {
        $client        = $this->prophesize(ClientInterface::class);
        $clientManager = $this->prophesize(ClientManagerInterface::class);

        $client->isActive()->shouldBeCalledOnce()->willReturn(true);
        $client->getGrants()->shouldBeCalled()->willReturn(['grant']);
        $client->isConfidential()->shouldBeCalled()->willReturn(true);
        $client->getSecret()->shouldBeCalled()->willReturn('secret');

        $clientManager->find('client-id')->willReturn($client);

        $this->assertValidateClient($clientManager, true);
    }

    public function testValidateClientWithUnmatchSecret()
    {
        $client        = $this->prophesize(ClientInterface::class);
        $clientManager = $this->prophesize(ClientManagerInterface::class);

        $client->isActive()->shouldBeCalledOnce()->willReturn(true);
        $client->getGrants()->shouldBeCalled()->willReturn(['grant']);
        $client->isConfidential()->shouldBeCalled()->willReturn(true);
        $client->getSecret()->shouldBeCalled()->willReturn('unmatch');

        $clientManager->find('client-id')->willReturn($client);

        $this->assertValidateClient($clientManager, false);
    }

    /**
     * @param ObjectProphecy|ClientManagerInterface $clientManager
     */
    private function assertValidateClient($clientManager, bool $expected)
    {
        $target = $this->getTarget($clientManager->reveal());

        $this->assertSame(
            $expected,
            $target->validateClient('client-id', 'secret', 'grant')
        );
    }
}
