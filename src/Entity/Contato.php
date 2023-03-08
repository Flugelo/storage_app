<?php

namespace App\Entity;

use App\Repository\ContatoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContatoRepository::class)]
class Contato
{
    public function __construct($titulo, $telefone, $email)
    {
        $this->titulo = $titulo;
        $this->telefone = $telefone;
        $this->email = $email;
        $this->fornecedors = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 75, nullable: false)]
    private ?string $titulo = null;

    #[ORM\Column(type: "string", length: 20, nullable: false)]
    private ?string $telefone = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $created_at = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTime $updated_at = null;

    #[ORM\ManyToMany(targetEntity: Fornecedor::class, mappedBy: 'Contato')]
    private Collection $fornecedors;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(string $telefone): self
    {
        $this->telefone = $telefone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }
    #[ORM\PrePersist]
    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(\DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getVelues(): array{
        return [
            "id" => $this->id,
            "titulo" => $this->titulo,
            "telefone" => $this->telefone,
            "email" => $this->email,
            "created_at" => $this->created_at,
        ];
    }

    #[ORM\PrePersist()]
    #[ORM\PreUpdate()]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTime();
        if($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return Collection<int, Fornecedor>
     */
    public function getFornecedors(): Collection
    {
        return $this->fornecedors;
    }

    public function addFornecedor(Fornecedor $fornecedor): self
    {
        if (!$this->fornecedors->contains($fornecedor)) {
            $this->fornecedors->add($fornecedor);
            $fornecedor->addContato($this);
        }

        return $this;
    }

    public function removeFornecedor(Fornecedor $fornecedor): self
    {
        if ($this->fornecedors->removeElement($fornecedor)) {
            $fornecedor->removeContato($this);
        }

        return $this;
    }
}
