<?php

namespace App\Entity;

use App\Repository\ProtudoShoppingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProtudoShoppingRepository::class)]
class ProdutoShopping
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ?int $qtt_product = null;

    #[ORM\ManyToOne(inversedBy: 'shopping')]
    private ?Produto $produto = null;

    #[ORM\ManyToOne(inversedBy: 'produtoShopping')]
    private ?Shopping $shopping = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'produtoShoppings')]
    private ?Estoque $estoque = null;

    /**
     * @param int|null $qtt_product
     * @param Produto|null $produto
     * @param Estoque|null $estoque
     * @param float|null $price
     */
    public function __construct(?int $qtt_product, ?Produto $produto, ?Estoque $estoque, ?float $price)
    {
        $this->qtt_product = $qtt_product;
        $this->produto = $produto;
        $this->estoque = $estoque;
        $this->price = $price;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQttProduct(): ?int
    {
        return $this->qtt_product;
    }

    public function setQttProduct(int $qtt_product): self
    {
        $this->qtt_product = $qtt_product;

        return $this;
    }


    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    public function setProduto(?Produto $produto): self
    {
        $this->produto = $produto;

        return $this;
    }


    public function getShopping(): ?Shopping
    {
        return $this->shopping;
    }

    public function setShopping(?Shopping $shopping): self
    {
        $this->shopping = $shopping;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEstoque(): ?estoque
    {
        return $this->estoque;
    }

    public function setEstoque(?Estoque $estoque): self
    {
        $this->estoque = $estoque;

        return $this;
    }
}
