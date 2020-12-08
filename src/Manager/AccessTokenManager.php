<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Manager;

use Doctrine\Persistence\ObjectManager;
use MezzioOAuthDoctrine\Model\AccessToken;
use MezzioOAuthDoctrine\Model\AccessTokenInterface;

final class AccessTokenManager
{
    use ManagerTrait;

    public function __construct(ObjectManager $om, string $class = AccessToken::class)
    {
        $this->om = $om;
        $this->class = $class;
    }

    public function save(AccessTokenInterface $accessToken)
    {
        $this->doSave($accessToken);
    }
}