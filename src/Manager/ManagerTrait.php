<?php

declare(strict_types=1);

namespace MezzioOAuthDoctrine\Manager;


use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

trait ManagerTrait
{
    protected ObjectManager $om;
    protected string $class;

    /**
     * @return ObjectRepository|DocumentRepository
     */
    public function getRepository(): ObjectRepository
    {
        return $this->om->getRepository($this->class);
    }

    /**
     * @param string $identifier
     * @return object|DocumentRepository|
     */
    public function find(string $identifier): ?object
    {
        return $this->getRepository()->find($identifier);
    }

    protected function doSave(object $object): void
    {
        $this->om->persist($object);
        $this->om->flush();
    }
}