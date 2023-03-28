<?php

namespace App\Entity;

use App\Repository\ArmazemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArmazemRepository::class)]
class Armazem
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

    #[ORM\OneToMany(mappedBy: 'armazem', targetEntity: Estoque::class)]
    private Collection $Estoque;


    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->Estoque = new ArrayCollection();
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
        $estoques = $this->getEstoque();
        $stocks = array();
        foreach ($estoques as $estoque) {
            $stocks[] = [
                'id' => $estoque->getId(),
                'produto' => [
                    'id' => $estoque->getProduto()->getId(),
                    'name' => $estoque->getProduto()->getName()
                ],
                'quantity' => $estoque->getQuantity(),
                'qtt_max' => $estoque->getQttMax(),
                'qtt_min' => $estoque->getQttMin(),
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'estoque' => $stocks,
            'created_at' => $this->created_at
        ];
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTime();
        if($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return Collection<int, Estoque>
     */
    public function getEstoque(): Collection
    {
        return $this->Estoque;
    }

    public function addEstoque(Estoque $estoque): self
    {
        if (!$this->Estoque->contains($estoque)) {
            $this->Estoque->add($estoque);
            $estoque->setArmazem($this);
        }

        return $this;
    }

    public function removeEstoque(Estoque $estoque): self
    {
        if ($this->Estoque->removeElement($estoque)) {
            // set the owning side to null (unless already changed)
            if ($estoque->getArmazem() === $this) {
                $estoque->setArmazem(null);
            }
        }

        return $this;
    }

}
