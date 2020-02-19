<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Deal
{
    const STATUS_EMAIL_SENT = 1;
    const STATUS_EMAIL_NOT_SENT = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $government;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $accountNumber;

    /**
     * @ORM\Column(type="integer", length=255)
     * @Assert\NotBlank()
     */
    protected $amount;

    /**
     * @ORM\Column(type="array", length=255)
     * @Assert\NotBlank()
     */
    protected $files;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank()
     */
    protected $statusEmail;

    public function __construct()
    {
        $this->statusEmail = static::STATUS_EMAIL_NOT_SENT;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getStatusEmail(): int
    {
        return $this->statusEmail;
    }

    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address): void
    {
        $this->address = $address;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCity($city): void
    {
        $this->city = $city;
    }

    public function getPostCode()
    {
        return $this->postCode;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPostCode($postCode): void
    {
        $this->postCode = $postCode;
    }

    public function getGovernment()
    {
        return $this->government;
    }

    public function setGovernment($government): void
    {
        $this->government = $government;
    }

    public function setStatusEmail(int $statusEmail): void
    {
        $this->statusEmail = $statusEmail;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    public function setAccountNumber($accountNumber): void
    {
        $this->accountNumber = $accountNumber;
    }

    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles($files): void
    {
        $this->files = $files;
    }
}