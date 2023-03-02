<?php

namespace App\Controller;

use App\Entity\Estoque;
use App\Repository\EstoqueRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EstoqueController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }

    #[Route('/api/estoque', name: 'app_estoque', methods: ["GET"])]
    public function index(): JsonResponse
    {
        $queryBuilder = $this->EM->getRepository(Estoque::class)->createQueryBuilder('e');
        $queryBuilder = $queryBuilder->select('e.id', 'e.name', 'e.description', 'e.created_at');
        $result = $queryBuilder->getQuery()->getResult();

        return $this->json($result, 200);
    }

    #[Route('/api/estoque/{id}', name: 'app_estoque_show', methods: ["GET"])]
    public function show(EstoqueRepository $repository, $id): JsonResponse
    {
        $estoque = $repository->find($id);

        if ($estoque === null) return $this->json(["message" => "Estoque não encontrado"], 404);


        return $this->json($estoque->getvalues(), 200);
    }

    #[Route('/api/estoque', name: "app_estoque_create", methods: ["POST"])]
    public function create(Request $request, EstoqueRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data['name'] === null || $data['qtd_max_stock'] == null || $data['qtd_min_stock'] == null) return $this->json(["message" => "Formulário incorreto"], 422);

        $name = $data["name"];
        $description = $data["description"];
        $qtt_max = $data['qtt_max'];
        $qtt_min = $data['qtt_min'];

        $estoque = new Estoque($name, $description, $qtt_max, $qtt_min);

        $repository->save($estoque, true);

        return $this->json(["message" => "Estoque criado com sucesso."], 200);
    }

    #[Route('/api/estoque/{id}', name: "app_estoque_update", methods: ["PUT", "PATCH"])]
    public function update(EstoqueRepository $repository, Request $request, $id): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if ($data['name'] === null) return $this->json(["message" => "Formulário incorreto"], 422);

        $estoque = $repository->find($id);

        if ($estoque === null) return $this->json(["message" => "Estoque não encontrado"], 404);

        $name = $data["name"];
        $description = $data["description"];
        $qtt_max = $data['qtt_max'];
        $qtt_min = $data['qtt_min'];
        $estoque->setName($name);
        $estoque->setDescription($description);
        $estoque->setQttMax($qtt_max);
        $estoque->setQttMin($qtt_min);

        $this->EM->persist($estoque);
        $this->EM->flush();

        return $this->json(["message" => "Estoque atualizado com sucesso."], 200);

    }

    #[Route('/api/estoque/{id}', name: "app_estoque_delete", methods: ["DELETE"])]
    public function delete(EstoqueRepository $repository, $id): JsonResponse
    {
        $estoque = $repository->find($id);

        if ($estoque === null) return $this->json(["message" => "Estoque não encontrado"], 404);

        $qtd = count($estoque->getProdutoHasEstoques());

        if ($qtd != 0) $this->json(["message" => "O estoque não pode ser deletado pois ha produtos contidos"], 403);

        $repository->remove($estoque, true);

        return $this->json(["message" => "Estoque deletado com sucesso."], 200);

    }
}
