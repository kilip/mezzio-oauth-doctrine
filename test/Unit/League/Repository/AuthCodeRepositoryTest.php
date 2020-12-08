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
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use MezzioOAuthDoctrine\Contracts\AuthorizationCodeManagerInterface;
use MezzioOAuthDoctrine\Contracts\ClientManagerInterface;
use MezzioOAuthDoctrine\Converter\ScopeConverterInterface;
use MezzioOAuthDoctrine\League\Entity\AuthCodeEntity;
use MezzioOAuthDoctrine\League\Repository\AuthCodeRepository;
use MezzioOAuthDoctrine\Model\AuthorizationCodeInterface;
use MezzioOAuthDoctrine\Model\ClientInterface;
use MezzioOAuthDoctrine\Model\Scope;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AuthCodeRepositoryTest extends TestCase
{
    use ProphecyTrait;

    /** @var AuthorizationCodeManagerInterface|ObjectProphecy */
    private $authCodeManager;

    /** @var ClientManagerInterface|ObjectProphecy */
    private $clientManager;

    /** @var ScopeConverterInterface|ObjectProphecy */
    private $scopeConverter;

    protected function setUp(): void
    {
        $this->authCodeManager = $this->prophesize(AuthorizationCodeManagerInterface::class);
        $this->clientManager   = $this->prophesize(ClientManagerInterface::class);
        $this->scopeConverter  = $this->prophesize(ScopeConverterInterface::class);
    }

    private function getTarget(): AuthCodeRepository
    {
        return new AuthCodeRepository(
            $this->authCodeManager->reveal(),
            $this->clientManager->reveal(),
            $this->scopeConverter->reveal()
        );
    }

    public function testGetNewAuthCode()
    {
        $this->assertInstanceOf(AuthCodeEntity::class, $this->getTarget()->getNewAuthCode());
    }

    public function testPersistThrowsExceptionWhenIdentifierExists()
    {
        $authCode       = $this->prophesize(AuthorizationCodeInterface::class)->reveal();
        $authCodeEntity = $this->prophesize(AuthCodeEntityInterface::class);

        $authCodeEntity->getIdentifier()
            ->willReturn('id')
            ->shouldBeCalledOnce();
        $this->authCodeManager->find('id')
            ->willReturn($authCode)
            ->shouldBeCalledOnce();

        $this->expectException(UniqueTokenIdentifierConstraintViolationException::class);
        $this->getTarget()->persistNewAuthCode($authCodeEntity->reveal());
    }

    public function testPersist()
    {
        $client       = $this->prophesize(ClientInterface::class);
        $clientEntity = $this->prophesize(ClientEntityInterface::class);
        $scope        = $this->prophesize(Scope::class)->reveal();
        $scopeEntity  = $this->prophesize(ScopeEntityInterface::class)->reveal();

        $clientEntity->getIdentifier()
            ->shouldBeCalledOnce()
            ->willReturn('client-id');

        $authCodeEntity = $this->prophesize(AuthCodeEntityInterface::class);
        $authCodeEntity->getIdentifier()
            ->willReturn('id')
            ->shouldBeCalledTimes(2);
        $authCodeEntity->getExpiryDateTime()
            ->shouldBeCalledOnce()
            ->willReturn(new DateTime());
        $authCodeEntity->getClient()
            ->shouldBeCalledOnce()
            ->willReturn($clientEntity->reveal());
        $authCodeEntity->getScopes()
            ->shouldBeCalledOnce()
            ->willReturn([$scopeEntity]);
        $authCodeEntity->getUserIdentifier()
            ->shouldBeCalledOnce()
            ->willReturn('user-id');

        $this->clientManager->find('client-id')
            ->shouldBeCalledOnce()
            ->willReturn($client->reveal());

        $this->scopeConverter->toDomainArray([$scopeEntity])
            ->shouldBeCalledOnce()
            ->willReturn([$scope]);

        $this->authCodeManager->find('id')
            ->shouldBeCalledOnce()
            ->willReturn(null);
        $this->authCodeManager->save(Argument::type(AuthorizationCodeInterface::class))
            ->shouldBeCalledOnce();

        $this->getTarget()->persistNewAuthCode($authCodeEntity->reveal());
    }

    public function testRevokeAuthCode()
    {
        $authCode = $this->prophesize(AuthorizationCodeInterface::class);

        $this->authCodeManager->find('code-id')
            ->shouldBeCalledOnce()
            ->willReturn($authCode);

        $authCode->revoke()
            ->shouldBeCalledOnce();
        $this->authCodeManager->save($authCode)
            ->shouldBeCalledOnce();

        $this->getTarget()->revokeAuthCode('code-id');
    }

    public function testIsAuthCodeRevokedOnNotFoundIdentifier()
    {
        $this->authCodeManager->find('code-id')
            ->shouldBeCalledOnce()
            ->willReturn(null);

        $this->assertTrue($this->getTarget()->isAuthCodeRevoked('code-id'));
    }

    public function testIsAuthCodeRevoked()
    {
        $authCode = $this->prophesize(AuthorizationCodeInterface::class);

        $this->authCodeManager->find('code-id')
            ->shouldBeCalledOnce()
            ->willReturn($authCode);

        $authCode->isRevoked()
            ->shouldBeCalledOnce()
            ->willReturn(false);

        $this->assertFalse($this->getTarget()->isAuthCodeRevoked('code-id'));
    }
}
