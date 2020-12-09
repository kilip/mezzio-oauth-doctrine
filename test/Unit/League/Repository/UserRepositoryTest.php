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
use MezzioOAuthDoctrine\Contracts\OAuthUserInterface;
use MezzioOAuthDoctrine\Contracts\UserConverterInterface;
use MezzioOAuthDoctrine\Contracts\UserEntityInterface;
use MezzioOAuthDoctrine\Contracts\UserResolverInterface;
use MezzioOAuthDoctrine\League\Repository\UserRepository;
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Grant;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class UserRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var ClientManagerInterface|ObjectProphecy */
    private $clientManager;
    /** @var UserConverterInterface|ObjectProphecy */
    private $userConverter;
    /** @var UserResolverInterface|ObjectProphecy */
    private $userResolver;

    protected function setUp(): void
    {
        $this->clientManager = $this->prophesize(ClientManagerInterface::class);
        $this->userConverter = $this->prophesize(UserConverterInterface::class);
        $this->userResolver  = $this->prophesize(UserResolverInterface::class);
    }

    private function getTarget(): UserRepository
    {
        return new UserRepository(
            $this->clientManager->reveal(),
            $this->userConverter->reveal(),
            $this->userResolver->reveal()
        );
    }

    public function testGetUser()
    {
        $client       = $this->prophesize(ClientInterface::class);
        $userEntity   = $this->prophesize(UserEntityInterface::class);
        $clientEntity = $this->prophesize(ClientEntityInterface::class);
        $userModel    = $this->prophesize(OAuthUserInterface::class);

        $this->userResolver->resolveUser('username', 'password', Argument::type(Grant::class), $client)
            ->shouldBeCalled()
            ->willReturn($userModel);

        $clientEntity->getIdentifier()
            ->shouldBeCalled()
            ->willReturn('client-id');
        $this->clientManager->find('client-id')
            ->shouldBeCalled()
            ->willReturn($client);
        $this->userConverter->toLeague($userModel)
            ->shouldBeCalled()
            ->willReturn($userEntity);

        $target = $this->getTarget();
        $target->getUserEntityByUserCredentials(
            'username',
            'password',
            'grant',
            $clientEntity->reveal()
        );
    }

    public function testGetUserReturnsNullWhenCLientNotExists()
    {
        $clientEntity = $this->prophesize(ClientEntityInterface::class);

        $clientEntity->getIdentifier()->willReturn('client-id');
        $this->clientManager->find('client-id')
            ->shouldBeCalled()
            ->willReturn(null);

        $target = $this->getTarget();
        $result = $target->getUserEntityByUserCredentials(
            'username',
            'password',
            'grant',
            $clientEntity->reveal()
        );
        $this->assertNull($result);
    }

    public function testGetUserReturnsNullWhenResolvedUserIsNull()
    {
        $client       = $this->prophesize(ClientInterface::class);
        $clientEntity = $this->prophesize(ClientEntityInterface::class);

        $this->userResolver->resolveUser('username', 'password', Argument::type(Grant::class), $client)
            ->shouldBeCalled()
            ->willReturn(null);

        $clientEntity->getIdentifier()
            ->shouldBeCalled()
            ->willReturn('client-id');
        $this->clientManager->find('client-id')
            ->shouldBeCalled()
            ->willReturn($client);

        $target = $this->getTarget();
        $target->getUserEntityByUserCredentials(
            'username',
            'password',
            'grant',
            $clientEntity->reveal()
        );
    }
}
