<?php

namespace PaymentApi\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'payments')]
class Payments
{
    #[ORM\Id, ORM\Column(type: 'integer'), ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: 'customers')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    private $customer;

    #[ORM\ManyToOne(targetEntity: 'methods')]
    #[ORM\JoinColumn(name: 'method_id', referencedColumnName: 'id')]
    private $paymentMethod;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private float $amount;

    #[ORM\Column(name: 'payment_date', type: 'datetime', nullable: false)]
    private \DateTime $paymentDate;

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the associated customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set the associated customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get the associated payment method
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set the associated payment method
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * Get the value of amount
     */ 
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */ 
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of paymentDate
     */ 
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set the value of paymentDate
     *
     * @return  self
     */ 
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }
}
