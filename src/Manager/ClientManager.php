<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Manager;


use Doctrine\Persistence\ObjectManager;
use MezzioOAuthDoctrine\Model\Client;

class ClientManager
{
    use ManagerTrait;

    public function __construct(ObjectManager $om, string $class)
    {
        $this->om = $om;
        $this->class = $class;
    }

    public function save(Client $client): void
    {
        $this->doSave($client);
    }
}