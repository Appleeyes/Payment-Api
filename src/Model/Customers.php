<?php

namespace PaymentApi\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity, ORM\Table(name: 'customers')]
class Customers
{
    #[ORM\Id, ORM\Column(type: 'integer'), ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $firstName;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $lastName;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $email;

    #[ORM\Column(name: 'is_active', type: 'boolean', nullable: false)]
    private bool $isActive;

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
     * Get the value of firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Get the value of lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the value of isActive
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the value of isActive
     *
     * @return  self
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }
}
