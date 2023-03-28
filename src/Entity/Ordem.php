<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Ordem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ?float $product_price = null;
    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(targetEntity: Output::class, inversedBy: 'ordem')]
    private ?Output $output = null;

    #[ORM\ManyToOne(inversedBy: 'ordem')]
    private ?Estoque $estoque = null;

    #[ORM\ManyToOne(inversedBy: 'ordems')]
    private ?Produto $produto = null;
    /**
     * @param float|null $product_price
     * @param int|null $quantity
     * @param Estoque|null $estoque
     */
    public function __construct(?float $product_price, ?int $quantity, ?Estoque $estoque)
    {
        $this->product_price = $product_price;
        $this->quantity = $quantity;
        $this->estoque = $estoque;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductPrice(): ?float
    {
        return $this->product_price;
    }

    public function setProductPrice(float $product_price): self
    {
        $this->product_price = $product_price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getOutput(): ?Output
    {
        return $this->output;
    }

    public function setOutput(?Output $output): self
    {
        $this->output = $output;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        if($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTime());
    }

    public function getEstoque(): ?Estoque
    {
        return $this->estoque;
    }

    public function setEstoque(?Estoque $estoque): self
    {
        $this->estoque = $estoque;

        return $this;
    }

    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    public function setProduto(?produto $produto): self
    {
        $this->produto = $produto;

        return $this;
    }


}
