<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoriaController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }


    #[Route('/api/categoria', name: 'app_categoria', methods: ["GET"])]
    public function index(): JsonResponse
    {
        $queryBuilder = $this->EM->getRepository(Categoria::class)->createQueryBuilder('c');
        $queryBuilder = $queryBuilder->select('c.id', 'c.name', 'c.description', 'c.created_at')->orderBy('c.id', 'DESC');;
        $results = $queryBuilder->getQuery()->getResult();

        return $this->json($results,200);
    }


    #[Route('/api/categoria/{id}', name: "app_categoria_show", methods: ["GET"])]
    public function show(CategoriaRepository $repository,$id):JsonResponse
    {
        $categoria = $repository->find($id);

        if($categoria === null) return $this->json(['message' => 'Categoria não encontrada'], 404);

        return $this->json($categoria->getValues(), 200);
    }


    #[Route('/api/categoria', name: "app_categoria_create", methods: ["POST"])]
    public function create(CategoriaRepository $repository,Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data['name'] === null) return $this->json(['message' => "Formulário incorreto"], 422);

        $name = $data['name'];
        $description = $data['description'];

        $categoria = new Categoria($name, $description);

        $repository->save($categoria, true);

        return $this->json(["message" => "Categoria criada com sucesso"], 200);
    }

    #[Route('/api/categoria/{id}', name: "app_categoria_update", methods: ["PUT", "PATCH"])]
    public function update(CategoriaRepository $repository, Request $request ,$id) : JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if($data['name'] === null) return $this->json(['message' => "Formulário incorreto"], 422);

        $categoria = $repository->find($id);

        if($categoria === null) return $this->json(['message' => 'Categoria não encontrada'], 404);

        $name = $data['name'];
        $description = $data['description'];

        $categoria->setName($name);
        $categoria->setDescription($description);

        $this->EM->persist($categoria);
        $this->EM->flush();

        return $this->json(["message" => "Categoria atualizada com sucesso"],200);
    }
    #[Route('/api/categora/{id}', name: "app_categoria_delete", methods: ["DELETE"])]
    public function delete(CategoriaRepository $repository, $id): JsonResponse
    {
        $categoria = $repository->find($id);

        if($categoria === null) return $this->json(['message' => 'Categoria não encontrada'], 404);

        $repository->remove($categoria, true);

        return $this->json(["message" => "Categoria deletada com sucesso"], 200);

    }


}
