<?php

namespace PaymentApi\Repository;

use Doctrine\ORM\EntityManager;
use PaymentApi\Model\Customers;

/**
 * CustomersRepositoryDoctrine
 */
class CustomersRepositoryDoctrine implements CustomersRepository
{
    private EntityManager $entityManager;
    
    /**
     * Method __construct
     *
     * @param EntityManager $entityManager [explicite description]
     *
     * @return void
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
        
    /**
     * customer store
     *
     * @param Customers $customer [explicite description]
     *
     * @return void
     */
    public function store(Customers $customer): void
    {
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }
    
    /**
     * customer update
     *
     * @param Customers $customer [explicite description]
     *
     * @return void
     */
    public function update(Customers $customer): void
    {
        $this->entityManager->persist($customer);
        $this->entityManager->flush();
    }
    
    /**
     * customer remove
     *
     * @param Customers $customer [explicite description]
     *
     * @return void
     */
    public function remove(Customers $customer): void
    {
        $this->entityManager->remove($customer);
        $this->entityManager->flush();
    }
    
    /**
     * customer findAll
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(Customers::class)
            ->findAll();    }
    
    /**
     * customer findById
     *
     * @param int $customerId [explicite description]
     *
     * @return Customers
     */
    public function findById(int $id): Customers|null
    {
        $customer = $this->entityManager->find(Customers::class, $id);
        return $customer;
    }
}
