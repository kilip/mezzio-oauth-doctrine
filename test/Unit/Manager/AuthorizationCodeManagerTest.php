<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Tests\Unit\Manager;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use MezzioOAuthDoctrine\Manager\AuthorizationCodeManager;
use MezzioOAuthDoctrine\Model\AuthorizationCode;
use MezzioOAuthDoctrine\Model\AuthorizationCodeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AuthorizationCodeManagerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectRepository|ObjectProphecy
     */
    private ObjectProphecy $repository;

    /**
     * @var ObjectManager|ObjectProphecy
     */
    private ObjectProphecy $om;

    public function setUp(): void
    {
        $this->om = $this->prophesize(ObjectManager::class);
        $this->repository = $this->prophesize(ObjectRepository::class);
    }

    private function createTarget(): AuthorizationCodeManager
    {
        $this->om->getRepository(AuthorizationCode::class)
            ->willReturn($this->repository->reveal());

        return new AuthorizationCodeManager(
            $this->om->reveal(),
            AuthorizationCode::class
        );
    }

    public function testGetRepository()
    {
        $target = $this->createTarget();

        $this->assertSame($this->repository->reveal(), $target->getRepository());
    }

    public function testFind()
    {
        $expected = $this->prophesize(AuthorizationCodeInterface::class)->reveal();

        $this->repository->find('id')
            ->shouldBeCalledOnce()
            ->willReturn($expected);

        $result = $this->createTarget()->find('id');
        $this->assertSame($expected, $result);
    }

    public function testSave()
    {
        $token = $this->prophesize(AuthorizationCodeInterface::class)->reveal();
        $this->om->persist($token)
            ->shouldBeCalledOnce();
        $this->om->flush()
            ->shouldBeCalledOnce();

        $this->createTarget()->save($token);
    }
}
