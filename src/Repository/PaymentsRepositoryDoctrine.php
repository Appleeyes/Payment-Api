<?php

namespace PaymentApi\Repository;

use Doctrine\ORM\EntityManager;
use PaymentApi\Model\Payments;

class PaymentsRepositoryDoctrine implements PaymentsRepository
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
        
    /**
     * payment store
     *
     * @param Payments $payment [explicite description]
     *
     * @return void
     */
    public function store(Payments $payment): void
    {
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
    }
    
    /**
     * payment update
     *
     * @param Payments $payment [explicite description]
     *
     * @return void
     */
    public function update(Payments $payment): void
    {
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
    }
    
    /**
     * payment remove
     *
     * @param Payments $payment [explicite description]
     *
     * @return void
     */
    public function remove(Payments $payment): void
    {
        $this->entityManager->remove($payment);
        $this->entityManager->flush();
    }
    
    /**
     * payment findAll
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(Payments::class)
            ->findAll();    }
    
    /**
     * payment findById
     *
     * @param int $paymentId [explicite description]
     *
     * @return Payments
     */
    public function findById(int $id): Payments|null
    {
        $payment = $this->entityManager->find(Payments::class, $id);
        return $payment;
    }
}
