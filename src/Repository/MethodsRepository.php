<?php 

namespace PaymentApi\Repository;

use PaymentApi\Model\Methods;

/**
 * MethodsRepository
 */
interface MethodsRepository
{    
    /**
     * Method store
     *
     * @param Methods $method [explicite description]
     *
     * @return void
     */
    public function store(Methods $method): void;
        
    /**
     * Method update
     *
     * @param Methods $method [explicite description]
     *
     * @return void
     */
    public function update(Methods $method): void;
        
    /**
     * Method remove
     *
     * @param Methods $method [explicite description]
     *
     * @return void
     */
    public function remove(Methods $method): void;
        
    /**
     * Method findAll
     *
     * @return array
     */
    public function findAll(): array;
    
    /**
     * Method findById
     *
     * @param int $methodId [explicite description]
     *
     * @return Methods
     */
    public function findById(int $methodId): Methods|null;

}