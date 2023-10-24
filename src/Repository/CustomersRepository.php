<?php

namespace PaymentApi\Repository;

use PaymentApi\Model\Methods;

interface CustomersRepository
{
    public function store(Methods $method): void;
    public function update(Methods $method): void;
    public function remove(Methods $method): void;
    public function findAll(): array;
    public function findById(int $methodId): Methods|null;
}
