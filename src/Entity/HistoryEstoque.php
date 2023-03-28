<?php

namespace App\Entity;

use App\Repository\HistoryEstoqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoryEstoqueRepository::class)]
class HistoryEstoque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $qtt_max = null;

    #[ORM\Column]
    private ?int $qtt_min = null;

    #[ORM\Column]
    private ?float $price_unit = null;

    #[ORM\ManyToOne(inversedBy: 'historyEstoques')]
    private ?Estoque $estoque = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    /**
     * @param int|null $quantity
     * @param int|null $qtt_max
     * @param int|null $qtt_min
     * @param float|null $price_unit
     * @param estoque|null $estoque
     */
    public function __construct(?int $quantity, ?int $qtt_max, ?int $qtt_min, ?float $price_unit, ?Estoque $estoque)
    {
        $this->quantity = $quantity;
        $this->qtt_max = $qtt_max;
        $this->qtt_min = $qtt_min;
        $this->price_unit = $price_unit;
        $this->estoque = $estoque;
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getQttMax(): ?int
    {
        return $this->qtt_max;
    }

    public function setQttMax(int $qtt_max): self
    {
        $this->qtt_max = $qtt_max;

        return $this;
    }

    public function getQttMin(): ?int
    {
        return $this->qtt_min;
    }

    public function setQttMin(int $qtt_min): self
    {
        $this->qtt_min = $qtt_min;

        return $this;
    }

    public function getPriceUnit(): ?float
    {
        return $this->price_unit;
    }

    public function setPriceUnit(float $price_unit): self
    {
        $this->price_unit = $price_unit;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
