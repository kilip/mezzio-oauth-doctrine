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

use DateTime;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MezzioOAuthDoctrine\Contracts\AccessTokenManagerInterface;
use MezzioOAuthDoctrine\Contracts\RefreshTokenManagerInterface;
use MezzioOAuthDoctrine\League\Entity\RefreshTokenEntity;
use MezzioOAuthDoctrine\League\Repository\RefreshTokenRepository;
use MezzioOAuthDoctrine\Model\AccessTokenInterface;
use MezzioOAuthDoctrine\Model\RefreshTokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class RefreshTokenRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var RefreshTokenManagerInterface|ObjectProphecy */
    private $refreshTokenManager;
    /** @var AccessTokenManagerInterface|ObjectProphecy */
    private $accessTokenManager;

    protected function setUp(): void
    {
        $this->refreshTokenManager = $this->prophesize(RefreshTokenManagerInterface::class);
        $this->accessTokenManager  = $this->prophesize(AccessTokenManagerInterface::class);
    }

    private function getTarget(): RefreshTokenRepository
    {
        return new RefreshTokenRepository(
            $this->refreshTokenManager->reveal(),
            $this->accessTokenManager->reveal()
        );
    }

    public function testGetNewRefreshToken()
    {
        $this->assertInstanceOf(
            RefreshTokenEntity::class,
            $this->getTarget()->getNewRefreshToken()
        );
    }

    public function testPersistNewRefreshTokenThrowsExceptionWhenRefreshTokenIdentifierExists()
    {
        $entity = $this->prophesize(RefreshTokenEntityInterface::class);
        $model  = $this->prophesize(RefreshTokenInterface::class);

        $entity->getIdentifier()->shouldBeCalled()->willReturn('entity-id');
        $this->refreshTokenManager->find('entity-id')
            ->shouldBeCalled()
            ->willReturn($model);

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->getTarget()->persistNewRefreshToken($entity->reveal());
    }

    public function testPersistNewRefreshTokenSuccessfully()
    {
        $entity            = $this->prophesize(RefreshTokenEntityInterface::class);
        $accessTokenEntity = $this->prophesize(AccessTokenEntityInterface::class);
        $accessToken       = $this->prophesize(AccessTokenInterface::class);

        $this->refreshTokenManager->find('entity-id')
            ->willReturn(null);
        $this->refreshTokenManager->save(Argument::type(RefreshTokenInterface::class));

        $accessTokenEntity->getIdentifier()
            ->shouldBeCalled()
            ->willReturn('access-token-id');
        $this->accessTokenManager->find('access-token-id')
            ->shouldBeCalled()
            ->willReturn($accessToken->reveal());

        // entity assertion
        $entity->getIdentifier()->shouldBeCalled()->willReturn('entity-id');
        $entity->getExpiryDateTime()
            ->shouldBeCalled()
            ->willReturn(new DateTime());
        $entity->getAccessToken()->shouldBeCalled()->willReturn($accessTokenEntity);

        $this->getTarget()->persistNewRefreshToken($entity->reveal());
    }

    public function testRevokeRefreshToken()
    {
        $model = $this->prophesize(RefreshTokenInterface::class);

        $this->refreshTokenManager->find('token-id')
            ->willReturn($model);
        $model->revoke()->shouldBeCalled();
        $this->refreshTokenManager->save($model)
            ->shouldBeCalled();

        $this->getTarget()->revokeRefreshToken('token-id');
    }

    public function testIsRefreshTokenRevokedReturnsTrueWhenModelNotFound()
    {
        $this->refreshTokenManager->find('token-id')
            ->willReturn(null);
        $target = $this->getTarget();
        $this->assertTrue($target->isRefreshTokenRevoked('token-id'));
    }

    public function testIsRefreshTokenRevoked()
    {
        $model = $this->prophesize(RefreshTokenInterface::class);

        $this->refreshTokenManager->find('token-id')
            ->willReturn($model);
        $model->isRevoked()->willReturn(false);

        $target = $this->getTarget();
        $this->assertFalse($target->isRefreshTokenRevoked('token-id'));
    }
}
