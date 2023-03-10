<?php

namespace App\Entity;

use App\Repository\EstoqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstoqueRepository::class)]
class Estoque
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 85, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $created_at = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $updated_at = null;

    #[ORM\OneToMany(targetEntity: "App\Entity\ProdutoHasEstoque", mappedBy: "estoque")]
    private Collection $produtoHasEstoques;


    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->produtoHasEstoques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }





    public function getValues(): array
    {
        $produtosHasEstoque = $this->getProdutoHasEstoques();
        $produtos = array();
        foreach ($produtosHasEstoque as $p_e) {
            array_push($produtos, $p_e->getProduto()->getValue());
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'produtos' => $produtos,
            'created_at' => $this->created_at
        ];
    }

    #[ORM\PrePersist()]
    #[ORM\PreUpdate()]
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
            $produtoHasEstoque->setEstoque($this);
        }

        return $this;
    }

    public function removeProdutoHasEstoque(ProdutoHasEstoque $produtoHasEstoque): self
    {
        if ($this->produtoHasEstoques->removeElement($produtoHasEstoque)) {
            // set the owning side to null (unless already changed)
            if ($produtoHasEstoque->getEstoque() === $this) {
                $produtoHasEstoque->setEstoque(null);
            }
        }

        return $this;
    }

}
