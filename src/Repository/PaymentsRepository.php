<?php

namespace PaymentApi\Repository;

use PaymentApi\Model\Payments;

/**
 * PaymentsRepository
 */
interface PaymentsRepository
{    
    /**
     * Method store
     *
     * @param Payments $payment [explicite description]
     *
     * @return void
     */
    public function store(Payments $payment): void;
        
    /**
     * Method update
     *
     * @param Payments $payment [explicite description]
     *
     * @return void
     */
    public function update(Payments $payment): void;
        
    /**
     * Method remove
     *
     * @param Payments $payment [explicite description]
     *
     * @return void
     */
    public function remove(Payments $payment): void;
        
    /**
     * Method findAll
     *
     * @return array
     */
    public function findAll(): array;
        
    /**
     * Method findById
     *
     * @param int $paymentId [explicite description]
     *
     * @return Payments
     */
    public function findById(int $paymentId): Payments|null;
}
