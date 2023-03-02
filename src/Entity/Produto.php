<?php

namespace App\Entity;

use App\Repository\ProdutoRepository;
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
    private $created_at;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Fornecedor")]
    #[ORM\JoinTable(name: "Produtofornecedor")]
    #[ORM\JoinColumn(name: "Produto_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "Fornecedor_id", referencedColumnName: "id")]
    private Collection $fornecedor;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Categoria")]
    #[ORM\JoinTable(name: "Produtocategoria")]
    #[ORM\JoinColumn(name: "Produto_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "Categoria_id", referencedColumnName: "id")]
    private Collection $categoria;

    #[ORM\OneToMany(targetEntity: "App\Entity\ProdutoHasEstoque", mappedBy: "produto")]
    private Collection $produtoHasEstoques;

    public function __construct($name, $description, $weight, $unit)
    {
        $this->fornecedor = new ArrayCollection();
        $this->categoria = new ArrayCollection();
        $this->produtoHasEstoques = new ArrayCollection();

        $this->name = $name;
        $this->description = $description;
        $this->unit = $unit;
        $this->weight = $weight;
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

    /**
     * @return Collection<int, fornecedor>
     */
    public function getFornecedor(): Collection
    {
        return $this->fornecedor;
    }

    /*
    * @return array;
    */
    public function getValue(): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'fornecedor' => $this->fornecedor->getValues(),
            'categoria' => $this->getCategorias(),
            'estoques' => $this->getProdutoHasEstoques()
        ];
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
    public function getCategorias(): Collection
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


}
