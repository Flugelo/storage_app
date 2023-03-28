<?php

namespace App\Controller;

use App\Entity\Armazem;
use App\Repository\ArmazemRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArmazemController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }

    #[Route('/api/armazem', name: 'app_armazem', methods: ["GET"])]
    public function index(): JsonResponse
    {
        $queryBuilder = $this->EM->getRepository(Armazem::class)->createQueryBuilder('a');
        $queryBuilder = $queryBuilder->select('a.id', 'a.name', 'a.description', 'a.created_at')->orderBy('a.id', 'DESC');
        $result = $queryBuilder->getQuery()->getResult();

        return $this->json($result, 200);
    }

    #[Route('/api/armazem/{id}', name: 'app_armazem_show', methods: ["GET"])]
    public function show(ArmazemRepository $repository, $id): JsonResponse
    {
        $armazem = $repository->find($id);

        if ($armazem === null) return $this->json(["message" => "Armazem não encontrado."], 404);


        return $this->json($armazem->getvalues(), 200);
    }

    #[Route('/api/armazem', name: "app_armazem_create", methods: ["POST"])]
    public function create(Request $request, ArmazemRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['description'])) return $this->json(["message" => "Formulário incorreto"], 422);

        $name = $data["name"];
        $description = $data["description"];
        $armazem = new Armazem($name, $description);
        $armazem->setCreatedAt(new \DateTime());
        $armazem->setUpdatedAt(new \DateTime());


        $repository->save($armazem, true);

        return $this->json(["message" => "Armazem cadastrado com sucesso."], 200);
    }

    #[Route('/api/armazem/{id}', name: "app_armazem_update", methods: ["PUT", "PATCH"])]
    public function update(ArmazemRepository $repository, Request $request, $id): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if ($data['name'] === null) return $this->json(["message" => "Formulário incorreto"], 422);

        $armazem = $repository->find($id);

        if ($armazem === null) return $this->json(["message" => "Armazem não encontrado"], 404);

        $name = $data["name"];
        $description = $data["description"];
        $armazem->setName($name);
        $armazem->setDescription($description);
        $armazem->setUpdatedAt(new \DateTime());
        $this->EM->persist($armazem);
        $this->EM->flush();

        return $this->json(["message" => "Armazem atualizado com sucesso."], 200);

    }

    #[Route('/api/armazem/{id}', name: "app_armazem_delete", methods: ["DELETE"])]
    public function delete(ArmazemRepository $repository, $id): JsonResponse
    {
        $armazem = $repository->find($id);

        if ($armazem === null) return $this->json(["message" => "Armazem não encontrado"], 404);

        $qtd = count($armazem->getEstoque());

        if ($qtd != 0) $this->json(["message" => "O armazem não pode ser deletado pois faz parte de um Estoque"], 403);

        $repository->remove($armazem, true);

        return $this->json(["message" => "Armazem deletado com sucesso."], 200);

    }

//    #[Route('/api/armazem/link/produto/{armazem_id}/{produto_id}', name: "api_armazem_link_produto", methods: ['GET'])]
//    public function linkToFornecedor($armazem_id, $produto_id, ArmazemRepository $armazemRepository, ProdutoRepository $produtoRepository): JsonResponse
//    {
//       $armazem = $armazemRepository->find($armazem_id);
//
//       if(!$armazem) return $this->json(['message' => 'Armazem não encontrado'], 404);
//
//       $produto = $produtoRepository->find($produto_id);
//
//       if(!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);
//
//    }
}
