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
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MezzioOAuthDoctrine\Contracts\AccessTokenManagerInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Converter\ScopeConverterInterface;
use MezzioOAuthDoctrine\League\Entity\AccessTokenEntity;
use MezzioOAuthDoctrine\League\Repository\AccessTokenRepository;
use MezzioOAuthDoctrine\Model\AccessTokenInterface;
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Scope;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AccessTokenRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var AccessTokenManagerInterface|MockObject */
    private $accessTokenManager;
    /** @var ClientManagerInterface|MockObject */
    private $clientManager;
    /** @var ScopeConverterInterface|MockObject */
    private $scopeConverter;

    protected function setUp(): void
    {
        $this->accessTokenManager = $this->prophesize(AccessTokenManagerInterface::class);
        $this->clientManager      = $this->prophesize(ClientManagerInterface::class);
        $this->scopeConverter     = $this->prophesize(ScopeConverterInterface::class);
    }

    private function getTarget(): AccessTokenRepository
    {
        return new AccessTokenRepository(
            $this->accessTokenManager->reveal(),
            $this->clientManager->reveal(),
            $this->scopeConverter->reveal()
        );
    }

    public function testGetNewToken()
    {
        $client = $this->createMock(ClientEntityInterface::class);
        $scope  = $this->createMock(ScopeEntityInterface::class);

        $result = $this->getTarget()->getNewToken($client, [$scope], 'user-id');
        $this->assertInstanceOf(AccessTokenEntity::class, $result);
        $this->assertSame($client, $result->getClient());
        $this->assertSame('user-id', $result->getUserIdentifier());
        $this->assertSame([$scope], $result->getScopes());
    }

    public function testPersistNewAccessToken()
    {
        $client = $this->prophesize(ClientInterface::class);
        $scope  = $this->prophesize(Scope::class);

        $clientEntity      = $this->prophesize(ClientEntityInterface::class);
        $accessTokenEntity = $this->prophesize(AccessTokenEntityInterface::class);
        $scopeEntity       = $this->prophesize(ScopeEntityInterface::class);
        $expiry            = new DateTime();

        $this->accessTokenManager->find('id')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->accessTokenManager->save(Argument::type(AccessTokenInterface::class))
            ->shouldBeCalledOnce();

        $this->clientManager
            ->find('client-id')
            ->shouldBeCalledOnce()
            ->willReturn($client->reveal());

        $clientEntity
            ->getIdentifier()
            ->shouldBeCalledOnce()
            ->willReturn('client-id');
        $accessTokenEntity->getClient()
            ->shouldBeCalledOnce()
            ->willReturn($clientEntity);
        $accessTokenEntity
            ->getIdentifier()
            ->shouldBeCalledTimes(2)
            ->willReturn('id');
        $accessTokenEntity
            ->getScopes()
            ->shouldBeCalledOnce()
            ->willReturn([$scopeEntity->reveal()]);
        $accessTokenEntity->getUserIdentifier()
            ->shouldBeCalledOnce()
            ->willReturn('user-id');
        $accessTokenEntity
            ->getExpiryDateTime()
            ->shouldBeCalledOnce()
            ->willReturn($expiry);

        $this->scopeConverter->toDomainArray([$scopeEntity->reveal()])
            ->shouldBeCalledOnce()
            ->willReturn([$scope]);

        $this->getTarget()->persistNewAccessToken($accessTokenEntity->reveal());
    }

    public function testPersistThrowsWhenAccessTokenAlreadyExists()
    {
        $accessTokenEntity = $this->prophesize(AccessTokenEntityInterface::class);
        $accessToken       = $this->prophesize(AccessTokenInterface::class);

        $accessTokenEntity->getIdentifier()
            ->willReturn('id')
            ->shouldBeCalledOnce();

        $this->accessTokenManager
            ->find('id')
            ->shouldBeCalledOnce()
            ->willReturn($accessToken->reveal());

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->getTarget()->persistNewAccessToken($accessTokenEntity->reveal());
    }

    public function testRevokeAccessToken()
    {
        $accessToken = $this->prophesize(AccessTokenInterface::class);

        $accessToken->revoke()
            ->shouldBeCalledOnce();

        $this->accessTokenManager
            ->find('id')
            ->shouldBeCalledOnce()
            ->willReturn($accessToken->reveal());

        $this->accessTokenManager
            ->save($accessToken->reveal())
            ->shouldBeCalledOnce();

        $this->getTarget()->revokeAccessToken('id');
    }

    public function testIsAccessTokenRevoked()
    {
        $accessToken = $this->prophesize(AccessTokenInterface::class);
        $accessToken->isRevoked()
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->accessTokenManager->find('token-id')
            ->shouldBeCalledOnce()
            ->willReturn($accessToken->reveal());

        $this->assertFalse($this->getTarget()->isAccessTokenRevoked('token-id'));
    }

    public function testIsAccessTokenRevokedShouldBeFalseWhenTokenNotFound()
    {
        $this->accessTokenManager->find('token-id')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->assertTrue($this->getTarget()->isAccessTokenRevoked('token-id'));
    }
}
