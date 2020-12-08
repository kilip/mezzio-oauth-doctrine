<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Manager;


use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use MezzioOAuthDoctrine\Model\AuthorizationCodeInterface;

class AuthorizationCodeManager
{
    use ManagerTrait;

    public function __construct(ObjectManager $om, string $class)
    {
        $this->om = $om;
        $this->class = $class;
    }

    public function save(AuthorizationCodeInterface $authorizationCode): void
    {
        $this->doSave($authorizationCode);
    }
}