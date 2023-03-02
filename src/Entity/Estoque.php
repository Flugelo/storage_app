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

    #[ORM\Column(type: "float", nullable: true)]
    private $qtt_max;

    #[ORM\Column(type: "float", nullable: true)]
    private $qtt_min;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(targetEntity: "App\Entity\ProdutoHasEstoque", mappedBy: "estoque")]
    private Collection $produtoHasEstoques;


    public function __construct($name, $description, $qtt_max, $qtt_min)
    {
        $this->name = $name;
        $this->description = $description;
        $this->qtt_min = $qtt_min;
        $this->qtt_max = $qtt_max;
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

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQttMax()
    {
        return $this->qtt_max;
    }

    /**
     * @param mixed $qtt_max
     */
    public function setQttMax($qtt_max): void
    {
        $this->qtt_max = $qtt_max;
    }

    /**
     * @return mixed
     */
    public function getQttMin()
    {
        return $this->qtt_min;
    }

    /**
     * @param mixed $qtt_min
     */
    public function setQttMin($qtt_min): void
    {
        $this->qtt_min = $qtt_min;
    }


    public function getvalues(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'qtt_min' => $this->qtt_min,
            'qtt_max' => $this->qtt_max,
            'description' => $this->description,
            'created_at' => $this->created_at
        ];
    }

    #[ORM\PrePersist()]
    #[ORM\PreUpdate()]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTimeImmutable();
        if ($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTimeImmutable());
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
