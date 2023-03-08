<?php

namespace App\Entity;

use App\Repository\ProdutoHasEstoqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProdutoHasEstoqueRepository::class)]
class ProdutoHasEstoque
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

    #[ORM\ManyToOne(targetEntity: "App\Entity\Estoque", inversedBy: "produtoHasEstoques")]
    #[ORM\JoinColumn(name: "estoque_id", referencedColumnName: "id", nullable: false)]
    private ?estoque $estoque = null;

    #[ORM\Column(type: "float", nullable: false)]
    private ?float $qtt_max = null;

    #[ORM\Column(type: "float", nullable: false)]
    private ?float $qtt_min = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $created_at;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $updated_at;

    /**
     * @param float|null $quantity
     * @param float|null $qtt_max
     * @param float|null $qtt_min
     * @param produto|null $produto
     * @param estoque|null $estoque
     */
    public function __construct(?float $quantity, ?produto $produto, ?estoque $estoque)
    {
        $this->produto = $produto;
        $this->quantity = $quantity;
        $this->estoque = $estoque;

    }


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

    public function getUnit(): ?int
    {
        return $this->unit;
    }

    public function setUnit(int $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getQtdMax(): ?float
    {
        return $this->qtd_max;
    }

    public function setQtdMax(float $qtd_max): self
    {
        $this->qtd_max = $qtd_max;

        return $this;
    }

    public function getQtdMin(): ?float
    {
        return $this->qtd_min;
    }

    public function setQtdMin(float $qtd_min): self
    {
        $this->qtd_min = $qtd_min;

        return $this;
    }

    public function getEstoque(): ?estoque
    {
        return $this->estoque;
    }

    public function setEstoque(?estoque $estoque): self
    {
        $this->estoque = $estoque;

        return $this;
    }

    public function getProduto(): ?produto
    {
        return $this->produto;
    }

    public function setProduto(?produto $produto): self
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
            'produto' => $this->getProduto()->getValue(),
            'quantity' => $this->quantity,
            'qtt_max' => $this->qtt_max,
            'qtt_min' => $this->qtt_min,
        ];
    }

}
