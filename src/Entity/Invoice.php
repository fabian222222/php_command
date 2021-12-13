<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceRepository::class)
 */
class Invoice
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
    private $reference;

    /**
     * @ORM\Column(type="date")
     */
    private $creation_date;

    /**
     * @ORM\Column(type="text")
     */
    private $client_informations;

    /**
     * @ORM\Column(type="text")
     */
    private $compagny_informations;

    /**
     * @ORM\OneToMany(targetEntity=InvoiceRow::class, mappedBy="invoice",cascade={"persist"})
     */
    private $invoiceRows;

    public function __construct()
    {
        $this->invoiceRows = new ArrayCollection();
        $this->creation_date = new \DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getClientInformations(): ?string
    {
        return $this->client_informations;
    }

    public function setClientInformations(string $client_informations): self
    {
        $this->client_informations = $client_informations;

        return $this;
    }

    public function getCompagnyInformations(): ?string
    {
        return $this->compagny_informations;
    }

    public function setCompagnyInformations(string $compagny_informations): self
    {
        $this->compagny_informations = $compagny_informations;

        return $this;
    }

    /**
     * @return Collection|InvoiceRow[]
     */
    public function getInvoiceRows(): Collection
    {
        return $this->invoiceRows;
    }

    public function addInvoiceRow(InvoiceRow $invoiceRow): self
    {
        if (!$this->invoiceRows->contains($invoiceRow)) {
            $this->invoiceRows[] = $invoiceRow;
            $invoiceRow->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceRow(InvoiceRow $invoiceRow): self
    {
        if ($this->invoiceRows->removeElement($invoiceRow)) {
            // set the owning side to null (unless already changed)
            if ($invoiceRow->getInvoice() === $this) {
                $invoiceRow->setInvoice(null);
            }
        }

        return $this;
    }
}
