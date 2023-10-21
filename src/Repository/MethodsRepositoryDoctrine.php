<?php

namespace PaymentApi\Repository;

use Doctrine\ORM\EntityManager;
use PaymentApi\Model\Methods;

class MethodsRepositoryDoctrine implements MethodsRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
        
    /**
     * Method store
     *
     * @param Methods $method [explicite description]
     *
     * @return void
     */
    public function store(Methods $method): void
    {
        $this->entityManager->persist($method);
        $this->entityManager->flush();
    }
    
    /**
     * Method update
     *
     * @param Methods $method [explicite description]
     *
     * @return void
     */
    public function update(Methods $method): void
    {
        // $this->em->persist($method);
        // $this->em->flush();
    }
    
    /**
     * Method remove
     *
     * @param Methods $method [explicite description]
     *
     * @return void
     */
    public function remove(Methods $method): void
    {
        $this->entityManager->remove($method);
        $this->entityManager->flush();
    }
    
    /**
     * Method findAll
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(Methods::class)
            ->findAll();    }
    
    /**
     * Method findById
     *
     * @param int $methodId [explicite description]
     *
     * @return Methods
     */
    public function findById(int $id): Methods|null
    {
        $method = $this->entityManager->find(Methods::class, $id);
        return $method;
    }
}
