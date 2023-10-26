<?php

namespace PaymentApi\Repository;

use PaymentApi\Model\Payments;

interface PaymentsRepository
{
    public function store(Payments $payment): void;
    public function update(Payments $payment): void;
    public function remove(Payments $payment): void;
    public function findAll(): array;
    public function findById(int $paymentId): Payments|null;
}
