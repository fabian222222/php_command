<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $client_fullname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $state;

    /**
     * @ORM\Column(type="date")
     */
    private $limit_date;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="command",cascade={"persist"})
     */
    private $products;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pay_check;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $last_invoice;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->pay_check = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientFullname(): ?string
    {
        return $this->client_fullname;
    }

    public function setClientFullname(string $client_fullname): self
    {
        $this->client_fullname = $client_fullname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getLimitDate(): ?\DateTimeInterface
    {
        return $this->limit_date;
    }

    public function setLimitDate(\DateTimeInterface $limit_date): self
    {
        $this->limit_date = $limit_date;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCommand($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeCommand($this);
        }

        return $this;
    }

    public function getPayCheck(): ?bool
    {
        return $this->pay_check;
    }

    public function setPayCheck(bool $pay_check): self
    {
        $this->pay_check = $pay_check;

        return $this;
    }

    public function getLastInvoice(): ?string
    {
        return $this->last_invoice;
    }

    public function setLastInvoice(?string $last_invoice): self
    {
        $this->last_invoice = $last_invoice;

        return $this;
    }
}
