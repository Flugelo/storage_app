<?php

namespace App\Entity;

use App\Repository\ShoppingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingRepository::class)]
class Shopping
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private $expected_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'shopping', targetEntity: ProdutoShopping::class)]
    private Collection $produtoShopping;

    #[ORM\Column]
    private ?float $total_price = null;

    /**
     * @param int|null $status
     * @param \DateTimeInterface|null $expected_date
     * @param string|null $description
     * @param float|null $total_price
     */
    public function __construct(?int $status, ?\DateTimeInterface $expected_date, ?string $description, ?float $total_price)
    {
        $this->status = $status;
        $this->expected_date = $expected_date;
        $this->description = $description;
        $this->total_price = $total_price;
        $this->produtoShopping = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExpectedDate(): ?\DateTimeInterface
    {
        return $this->expected_date;
    }

    public function setExpectedDate(?\DateTimeInterface $expected_date): self
    {
        $this->expected_date = $expected_date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
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

    /**
     * @return Collection<int, produtoShopping>
     */
    public function getProdutoShopping(): Collection
    {
        return $this->produtoShopping;
    }

    public function addProdutoShopping(ProdutoShopping $produtoShopping): self
    {
        if (!$this->produtoShopping->contains($produtoShopping)) {
            $this->produtoShopping->add($produtoShopping);
            $produtoShopping->setShopping($this);
        }

        return $this;
    }

    public function removeProdutoShopping(ProdutoShopping $produtoShopping): self
    {
        if ($this->produtoShopping->removeElement($produtoShopping)) {
            // set the owning side to null (unless already changed)
            if ($produtoShopping->getShopping() === $this) {
                $produtoShopping->setShopping(null);
            }
        }

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->total_price;
    }

    public function setTotalPrice(float $total_price): self
    {
        $this->total_price = $total_price;

        return $this;
    }

    public function getValues(): array
    {
        $produtos = array();
        foreach ($this->getProdutoShopping() as $produto){
            $produtos[] = [
                'id' => $produto->getProduto()->getId(),
                'name' => $produto->getProduto()->getName(),
                'quantity' => $produto->getQttProduct(),
                'price' => $produto->getPrice(),
                'estoque' => [
                    'id' => $produto->getEstoque()->getId(),
                    'name' => $produto->getEstoque()->getArmazem()->getName(),
                ]
            ];
        }
        return [
            'id' => $this->id,
            'status' => $this->status,
            'expected_date' => date_format($this->expected_date, 'Y-m-d'),
            'description' => $this->description,
            'created_at' => date_format($this->created_at, 'Y-m-d'),
            'total_price' => $this->total_price,
            'produtos' => $produtos
        ];
    }

}
