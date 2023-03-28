<?php

namespace App\Entity;

use App\Repository\OutputRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutputRepository::class)]
class Output
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $subtotal = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(nullable: true)]
    private ?float $discount = null;

    #[ORM\Column(length: 255)]
    private ?string $payment_method = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'output', targetEntity: Ordem::class)]
    private Collection $ordens;

    /**
     * @param float|null $subtotal
     * @param float|null $total
     * @param float|null $discount
     * @param string|null $payment_method
     */
    public function __construct(?float $subtotal, ?float $total, ?float $discount, ?string $payment_method)
    {
        $this->subtotal = $subtotal;
        $this->total = $total;
        $this->discount = $discount;
        $this->payment_method = $payment_method;
        $this->ordens = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->payment_method;
    }

    public function setPaymentMethod(string $payment_method): self
    {
        $this->payment_method = $payment_method;

        return $this;
    }

    /**
     * @return Collection<int, Ordem>
     */
    public function getOrde(): Collection
    {
        return $this->ordens;
    }

    public function addOrde(Ordem $orde): self
    {
        if (!$this->ordens->contains($orde)) {
            $this->ordens->add($orde);
            $orde->setOutput($this);
        }

        return $this;
    }

    public function removeOrde(Ordem $orde): self
    {
        if ($this->ordens->removeElement($orde)) {
            // set the owning side to null (unless already changed)
            if ($orde->getOutput() === $this) {
                $orde->setOutput(null);
            }
        }

        return $this;
    }

    public function getValues(): array
    {
        $orders = array();
        foreach ($this->ordens as $ordem){

            $orders[] = [
                'id' => $ordem->getId(),
                'product_name' => $ordem->getProduto()->getName(),
                'product_unit' => $ordem->getProduto()->getUnit(),
                'product_weight' => $ordem->getProduto()->getWeight(),
                'product_price' => $ordem->getProductPrice(),
                'armazem_name' => $ordem->getEstoque()->getArmazem()->getName(),
                'quantity' => $ordem->getQuantity(),
            ];
        }
        return [
            'id' => $this->id,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'order' => $orders,
            'created_at' => date_format($this->created_at, 'd/m/y H:i'),
        ];
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTime();
        if($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTime());
    }
}
