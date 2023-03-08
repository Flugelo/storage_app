<?php

namespace App\Controller;

use App\Entity\Contato;
use App\Entity\Fornecedor;
use App\Repository\ContatoRepository;
use App\Repository\FornecedorRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FornecedorController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }

    #[Route('/api/fornecedor', name: 'app_fornecedor', methods: ['GET'])]
    public function index(FornecedorRepository $fornecedorRepository): JsonResponse
    {

        $query = $fornecedorRepository->createQueryBuilder('f');
        $query = $query->select('f.id', 'f.fantasia', 'f.razao_social', 'f.cnpj', 'f.responsavel', 'f.status', 'f.created_at');
        $result = $query->getQuery()->getResult();

        return $this->json($result, 200);
    }

    #[Route('/api/fornecedor/{id}', name: 'app_fornecedor_show', methods: ['GET'])]
    public function show(FornecedorRepository $repository, $id): JsonResponse
    {

        $fornecedor = $repository->findOneBy(['id' => $id]);

        if ($fornecedor === null) return $this->json(['message' => 'Fornecedor não encontrado'], 404);

        $fornecedor = $fornecedor->getValues();

        return $this->json($fornecedor, 200);
    }

    #[Route('/api/fornecedor', name: 'app_fornecedor_create', methods: ['POST'])]
    public function create(Request $request, FornecedorRepository $fornecedorRepository): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if ($data['fantasia'] == null || $data['cnpj'] == null || $data['status'] == null) return $this->json(['message' => 'Formulário incorreto'], 422);

        $fantasia = $data['fantasia'];
        $razao_social = $data['razao_social'];
        $cnpj = $data['cnpj'];
        $responsavel = $data['responsavel'];
        $status = $data['status'];

        $check = $fornecedorRepository->findBy(['cnpj' => $cnpj]);

        if ($check != null) return $this->json(['message' => 'Já existe um fornecedor com esse CNPJ'], 409);

        $fornecedor = new Fornecedor($fantasia, $razao_social, $cnpj, $responsavel, $status);
        $fornecedorRepository->save($fornecedor, true);

        return $this->json(['message' => 'Fornecedor criado com sucesso', $fornecedor->getValues()], 200);
    }


    #[Route('/api/fornecedor/{id}', name: 'app_fornecedor_update', methods: ['PUT', 'PATCH'])]
    public function update(FornecedorRepository $fornecedorRepository, $id, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        // return $this->json($data);
        if ($data['fantasia'] === null || $data['cnpj'] === null || $data['status'] === null) return $this->json(['message' => 'Formulário incorreto'], 422);

        $fantasia = $data['fantasia'];
        $razao_social = $data['razao_social'];
        $cnpj = $data['cnpj'];
        $responsavel = $data['responsavel'];
        $status = $data['status'];

        $fornecedor = $fornecedorRepository->find($id);

        if($fornecedor === null) return $this->json(["message" => "Fornecedor não encontrado"],404);


        $fornecedor->setFantasia($fantasia);
        $fornecedor->setRazaoSocial($razao_social);
        $fornecedor->setCnpj($cnpj);
        $fornecedor->setResponsavel($responsavel);
        $fornecedor->setStatus($status);
        $this->EM->persist($fornecedor);
        $this->EM->flush();

        return $this->json(["message" => "Fornecedor atualizado com sucesso."], 200);
    }

    #[Route('/api/fornecedor/{id}', name: 'app_fornecedor_delete', methods: ['DELETE'])]
    public function delete(FornecedorRepository $repository,$id): JsonResponse
    {
        $fornecedor = $repository->find($id);

        if (!$fornecedor) return $this->json('Fornecedor não encontrado', 404);

        $this->EM->remove($fornecedor);
        $this->EM->flush();

        return $this->json(['message' => "Fornecedor deletado com sucesso."], 200);
    }
}
