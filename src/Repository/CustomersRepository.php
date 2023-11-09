<?php

namespace PaymentApi\Repository;

use PaymentApi\Model\Customers;

/**
 * CustomersRepository
 */
interface CustomersRepository
{    
    /**
     * Method store
     *
     * @param Customers $customer [explicite description]
     *
     * @return void
     */   
    public function store(Customers $customer): void;  
  
    /**
     * Method update
     *
     * @param Customers $customer [explicite description]
     *
     * @return void
     */
    public function update(Customers $customer): void;
        
    /**
     * Method remove
     *
     * @param Customers $customer [explicite description]
     *
     * @return void
     */
    public function remove(Customers $customer): void;
        
    /**
     * Method findAll
     *
     * @return array
     */
    public function findAll(): array;
        
    /**
     * Method findById
     *
     * @param int $customerId [explicite description]
     *
     * @return Customers
     */
    public function findById(int $customerId): Customers|null;
}
