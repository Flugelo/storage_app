<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 75, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $updated_at = null;
    private Collection $produtos;

    public function __construct($name, $description = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->produtos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $nome): self
    {
        $this->name = $nome;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $descricao): self
    {
        $this->description = $descricao;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }



    public function getValues(): array{
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTimeImmutable();
        if($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTimeImmutable());
    }

    /**
     * @return Collection<int, Produto>
     */
    public function getProdutos(): Collection
    {
        return $this->produtos;
    }

    public function addProduto(Produto $produto): self
    {
        if (!$this->produtos->contains($produto)) {
            $this->produtos->add($produto);
            $produto->addCategorium($this);
        }

        return $this;
    }

    public function removeProduto(Produto $produto): self
    {
        if ($this->produtos->removeElement($produto)) {
            $produto->removeCategorium($this);
        }

        return $this;
    }


}
