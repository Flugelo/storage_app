<?php

namespace App\Entity;

use App\Repository\EstoqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstoqueRepository::class)]
class Estoque
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;
    
    #[ORM\Column(type: "integer", nullable: false)]
    private ?float $quantity = null;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Produto", inversedBy: "produtoHasEstoques")]
    #[ORM\JoinColumn(name: "produto_id", referencedColumnName: "id", nullable: false)]
    private ?produto $produto = null;

    #[ORM\Column(type: "float", nullable: false)]
    private ?float $qtt_max = null;

    #[ORM\Column(type: "float", nullable: false)]
    private ?float $qtt_min = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $created_at;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $updated_at;

    #[ORM\ManyToOne(inversedBy: 'Estoque')]
    private ?Armazem $armazem = null;

    #[ORM\Column]
    private ?float $unit_price = null;

    #[ORM\OneToMany(mappedBy: 'Estoque', targetEntity: Ordem::class)]
    private Collection $ordems;

    #[ORM\OneToMany(mappedBy: 'Estoque', targetEntity: HistoryEstoque::class)]
    private Collection $historyEstoques;

    #[ORM\OneToMany(mappedBy: 'Estoque', targetEntity: ProdutoShopping::class)]
    private Collection $produtoShoppings;

    /**
     * @param float|null $quantity
     * @param produto|null $produto
     * @param float|null $qtt_max
     * @param float|null $qtt_min
     * @param Armazem|null $armazem
     * @param float|null $unit_price
     */
    public function __construct(?float $quantity, ?produto $produto, ?float $qtt_max, ?float $qtt_min, ?Armazem $armazem, ?float $unit_price)
    {
        $this->quantity = $quantity;
        $this->produto = $produto;
        $this->qtt_max = $qtt_max;
        $this->qtt_min = $qtt_min;
        $this->armazem = $armazem;
        $this->unit_price = $unit_price;
        $this->ordems = new ArrayCollection();
        $this->historyEstoques = new ArrayCollection();
        $this->produtoShoppings = new ArrayCollection();
    }

    /**
     * @param float|null $quantity
     * @param float|null $qtt_max
     * @param float|null $qtt_min
     * @param produto|null $produto
     * @param Armazem|null $armazem
     */



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

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

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime|null $created_at
     */
    public function setCreatedAt(?\DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTime|null $updated_at
     */
    public function setUpdatedAt(?\DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTime();
        if($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTime());
    }

    public function getQttMax(): ?float
    {
        return $this->qtt_max;
    }

    public function setQttMax(float $qtt_max): self
    {
        $this->qtt_max = $qtt_max;

        return $this;
    }

    public function getQttMin(): ?float
    {
        return $this->qtt_min;
    }

    public function setQttMin(float $qtt_min): self
    {
        $this->qtt_min = $qtt_min;

        return $this;
    }

    public function getValues(): array{
        
        return [
            'id' => $this->id,
            'produto' => [
                'id' => $this->getProduto()->getId(),
                'name' => $this->getProduto()->getName(),
                'description' => $this->getProduto()->getDescription(),
                'unit' => $this->getProduto()->getUnit(),
                'weight' => $this->getProduto()->getWeight()
            ],
            'armazem' => [
                'id' => $this->getArmazem()->getId(),
                'name' => $this->getArmazem()->getName()
            ],
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'qtt_max' => $this->qtt_max,
            'qtt_min' => $this->qtt_min,
        ];
    }

    public function getArmazem(): ?Armazem
    {
        return $this->armazem;
    }

    public function setArmazem(?Armazem $armazem): self
    {
        $this->armazem = $armazem;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unit_price;
    }

    public function setUnitPrice(float $unit_price): self
    {
        $this->unit_price = $unit_price;

        return $this;
    }

    public function subtractQuantity(int $quantity): void
    {
        $this->quantity = $this->quantity - $quantity;
    }

    /**
     * @return Collection<int, Ordem>
     */
    public function getOrdems(): Collection
    {
        return $this->ordems;
    }

    public function addOrdem(Ordem $ordem): self
    {
        if (!$this->ordems->contains($ordem)) {
            $this->ordems->add($ordem);
            $ordem->setEstoque($this);
        }

        return $this;
    }

    public function removeOrdem(Ordem $ordem): self
    {
        if ($this->ordems->removeElement($ordem)) {
            // set the owning side to null (unless already changed)
            if ($ordem->getEstoque() === $this) {
                $ordem->setEstoque(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoryEstoque>
     */
    public function getHistoryEstoques(): Collection
    {
        return $this->historyEstoques;
    }

    public function addHistoryEstoque(HistoryEstoque $historyEstoque): self
    {
        if (!$this->historyEstoques->contains($historyEstoque)) {
            $this->historyEstoques->add($historyEstoque);
            $historyEstoque->setEstoque($this);
        }

        return $this;
    }

    public function removeHistoryEstoque(HistoryEstoque $historyEstoque): self
    {
        if ($this->historyEstoques->removeElement($historyEstoque)) {
            // set the owning side to null (unless already changed)
            if ($historyEstoque->getEstoque() === $this) {
                $historyEstoque->setEstoque(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProdutoShopping>
     */
    public function getProdutoShoppings(): Collection
    {
        return $this->produtoShoppings;
    }

    public function addProdutoShopping(ProdutoShopping $produtoShopping): self
    {
        if (!$this->produtoShoppings->contains($produtoShopping)) {
            $this->produtoShoppings->add($produtoShopping);
            $produtoShopping->setEstoque($this);
        }

        return $this;
    }

    public function removeProdutoShopping(ProdutoShopping $produtoShopping): self
    {
        if ($this->produtoShoppings->removeElement($produtoShopping)) {
            // set the owning side to null (unless already changed)
            if ($produtoShopping->getEstoque() === $this) {
                $produtoShopping->setEstoque(null);
            }
        }

        return $this;
    }

}
