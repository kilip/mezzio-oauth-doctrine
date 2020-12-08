<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Tests\Unit\Manager;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use MezzioOAuthDoctrine\Manager\AccessTokenManager;
use MezzioOAuthDoctrine\Model\AccessToken;
use MezzioOAuthDoctrine\Model\AccessTokenInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AccessTokenManagerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectManager|ObjectProphecy
     */
    private ObjectProphecy $om;

    /**
     * @var AccessTokenManager
     */
    private AccessTokenManager $target;

    /**
     * @var ObjectRepository|ObjectProphecy
     */
    private ObjectProphecy $repository;

    public function setUp(): void
    {
        $this->repository = $this->prophesize(ObjectRepository::class);
        $this->om = $this->prophesize(ObjectManager::class);

        $this->om->getRepository(AccessToken::class)
            ->willReturn($this->repository->reveal());

        $this->target = new AccessTokenManager(
            $this->om->reveal(),
            AccessToken::class
        );
    }

    public function testGetRepository()
    {
        $this->assertSame($this->repository->reveal(), $this->target->getRepository());
    }

    public function testFind()
    {
        $token = $this->prophesize(AccessTokenInterface::class);
        $this->repository->find('id')
            ->willReturn($token->reveal())
            ->shouldBeCalledOnce();
        $this->assertSame($token->reveal(), $this->target->find('id'));
    }

    public function testSave()
    {
        $token = $this->prophesize(AccessTokenInterface::class);

        $this->om->persist($token->reveal())
            ->shouldBeCalledOnce();
        $this->om->flush()
            ->shouldBeCalledOnce();

        $this->target->save($token->reveal());
    }
}
