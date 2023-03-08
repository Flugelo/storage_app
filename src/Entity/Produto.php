<?php

namespace App\Entity;

use App\Repository\ProdutoRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProdutoRepository::class)]
class Produto
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "smallint", nullable: true)]
    private ?int $unit = null;

    #[ORM\Column(type: "float", nullable: false)]
    private ?float $weight = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTime $created_at;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTime $updated_at;

    #[ORM\OneToMany(targetEntity: "App\Entity\ProdutoHasEstoque", mappedBy: "produto")]
    private Collection $produtoHasEstoques;

    #[ORM\ManyToMany(targetEntity: Fornecedor::class, inversedBy: 'produtos')]
    private Collection $fornecedor;

    #[ORM\ManyToMany(targetEntity: Categoria::class, inversedBy: 'produtos')]
    private Collection $categoria;

    public function __construct($name, $description, $weight, $unit)
    {

        $this->name = $name;
        $this->description = $description;
        $this->unit = $unit;
        $this->weight = $weight;
        $this->fornecedor = new ArrayCollection();
        $this->categoria = new ArrayCollection();
        $this->produtoHasEstoques = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }


    /*
    * @return array;
    */
    public function getValue(): array
    {
        $categorias = array();
        foreach ($this->getCategoria() as $categoria) {
            array_push($categorias, $categoria->getValues());
        }

        $estoques = array();
        // dd($this->getProdutoHasEstoques());
        foreach ($this->getProdutoHasEstoques() as $stock) {
            array_push($estoques, ['id' => $stock->getEstoque()->getId(), 'name' => $stock->getEstoque()->getName()]);
        }

        $fornecedores = array();

        foreach ($this->getFornecedor() as $fornecedor) {
            array_push($fornecedores, ['id' => $fornecedor->getId(), 'fantasia' => $fornecedor->getFantasia()]);
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'fornecedor' => $fornecedores,
            'categoria' => $categorias,
            'estoques' => $estoques,
        ];
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    /**
     * @param DateTime|null $created_at
     */
    public function setCreatedAt(?DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    /**
     * @param DateTime|null $updated_at
     */
    public function setUpdatedAt(?DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTime();
        if ($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTime());
    }


    /**
     * @return Collection<int, ProdutoHasEstoque>
     */
    public function getProdutoHasEstoques(): Collection
    {
        return $this->produtoHasEstoques;
    }

    public function addProdutoHasEstoque(ProdutoHasEstoque $produtoHasEstoque): self
    {
        if (!$this->produtoHasEstoques->contains($produtoHasEstoque)) {
            $this->produtoHasEstoques->add($produtoHasEstoque);
            $produtoHasEstoque->setProduto($this);
        }

        return $this;
    }

    public function removeProdutoHasEstoque(ProdutoHasEstoque $produtoHasEstoque): self
    {
        if ($this->produtoHasEstoques->removeElement($produtoHasEstoque)) {
            // set the owning side to null (unless already changed)
            if ($produtoHasEstoque->getProduto() === $this) {
                $produtoHasEstoque->setProduto(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, fornecedor>
     */
    public function getFornecedor(): Collection
    {
        return $this->fornecedor;
    }

    public function addFornecedor(fornecedor $fornecedor): self
    {
        if (!$this->fornecedor->contains($fornecedor)) {
            $this->fornecedor->add($fornecedor);
        }

        return $this;
    }

    public function removeFornecedor(fornecedor $fornecedor): self
    {
        $this->fornecedor->removeElement($fornecedor);

        return $this;
    }

    /**
     * @return Collection<int, categoria>
     */
    public function getCategoria(): Collection
    {
        return $this->categoria;
    }

    public function addCategorium(categoria $categorium): self
    {
        if (!$this->categoria->contains($categorium)) {
            $this->categoria->add($categorium);
        }

        return $this;
    }

    public function removeCategorium(categoria $categorium): self
    {
        $this->categoria->removeElement($categorium);

        return $this;
    }
}
