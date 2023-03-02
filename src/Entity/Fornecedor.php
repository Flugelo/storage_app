<?php

namespace App\Entity;

use App\Repository\FornecedorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: FornecedorRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Fornecedor
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    #[NotBlank]
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $fantasia = null;

    #[NotBlank]
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $razao_social = null;

    #[NotBlank]
    #[ORM\Column(type: "bigint", nullable: false, options: ["unsigned"=>true,"zerofill"=>true])]
    private ?string $cnpj = null;

    #[ORM\Column(type: "string", length: 76, nullable: true)]
    private ?string $responsavel = null;

    #[ORM\Column(type: "boolean", nullable: false, options: ["default"=>1])]
    private ?bool $status = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: "datetime", nullable: true, options: ["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToMany(targetEntity: "App\Entity\Contato")]
    #[ORM\JoinTable(name: "Fornecedorcontato")]
    #[ORM\JoinColumn(name: "Fornecedor_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "Contato_id", referencedColumnName: "id")]
    private Collection $contato;
    private Collection $produtos;


    /**
     * @param string|null $fantasia
     * @param string|null $razao_social
     * @param string|null $cnpj
     * @param string|null $responsavel
     * @param bool|null $status
     */
    public function __construct(?string $fantasia, ?string $razao_social, ?string $cnpj, ?string $responsavel, ?bool $status)
    {
        $this->fantasia = $fantasia;
        $this->razao_social = $razao_social;
        $this->cnpj = $cnpj;
        $this->responsavel = $responsavel;
        $this->status = $status;
        $this->contato = new ArrayCollection();
        $this->produtos = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFantasia(): ?string
    {
        return $this->fantasia;
    }

    public function setFantasia(string $fantasia): self
    {
        $this->fantasia = $fantasia;

        return $this;
    }

    public function getRazaoSocial(): ?string
    {
        return $this->razao_social;
    }

    public function setRazaoSocial(?string $razao_social): self
    {
        $this->razao_social = $razao_social;

        return $this;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj): self
    {
        $this->cnpj = $cnpj;

        return $this;
    }

    public function getResponsavel(): ?string
    {
        return $this->responsavel;
    }

    public function setResponsavel(?string $responsavel): self
    {
        $this->responsavel = $responsavel;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateUpdatedAt(): void
    {
        $this->updated_at = new \DateTimeImmutable();
        if($this->getCreatedAt() === null)
            $this->setCreatedAt(new \DateTimeImmutable());
    }

    public function getValues(): array{
        return [
            "id" => $this->id,
            "fantasia" => $this->fantasia,
            "cnpj" => $this->cnpj,
            "razao_social" => $this->razao_social,
            "responsavel" => $this->responsavel,
            "created_at" => $this->created_at,
        ];
    }

    /**
     * @return Collection<int, contato>
     */
    public function getContato(): Collection
    {
        return $this->contato;
    }

    public function addContato(contato $contato): self
    {
        if (!$this->contato->contains($contato)) {
            $this->contato->add($contato);
        }

        return $this;
    }

    public function removeContato(contato $contato): self
    {
        $this->contato->removeElement($contato);

        return $this;
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
            $produto->addFornecedor($this);
        }

        return $this;
    }

    public function removeProduto(Produto $produto): self
    {
        if ($this->produtos->removeElement($produto)) {
            $produto->removeFornecedor($this);
        }

        return $this;
    }

}
