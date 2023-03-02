<?php

namespace App\Controller;

use App\Entity\Produto;
use App\Entity\ProdutoHasEstoque;
use App\Repository\CategoriaRepository;
use App\Repository\EstoqueRepository;
use App\Repository\FornecedorRepository;
use App\Repository\ProdutoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProdutoController extends ApiController
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }

    #[Route('/api/produto', name: 'app_produto', methods: ["GET"])]
    public function index(): JsonResponse
    {
        $queryBuilder = $this->EM->getRepository(Produto::class)->createQueryBuilder('p');
        $queryBuilder = $queryBuilder->select('p.id', 'p.name', 'p.description', 'p.created_at', 'c.name as categoria', 'f.fantasia as fornecedor')
            ->leftJoin('p.categoria', 'c')
            ->leftJoin('p.fornecedor', 'f')
            ->leftJoin('p.produtoHasEstoques', 'p_e')
            ->leftJoin('p_e.estoque', 'e');

        $result = $queryBuilder->getQuery()->getResult();

        return $this->json($result,200);
    }

    #[Route('/api/produto/{id}', name: 'app_produto_show', methods: ["GET"])]
    public function show(ProdutoRepository $repository, $id): JsonResponse
    {
        $produto = $repository->find($id);

        if ($produto === null) return $this->json(["message" => "Produto não encontrado"], 404);

        return $this->json($produto->getValue(), 200);
    }
    #[Route('/api/produto', name: "app_produto_create", methods: ["POST"])]
    public function create(ProdutoRepository    $repository,
                           FornecedorRepository $fornecedorRepository,
                           CategoriaRepository  $categoriaRepository,
                           EstoqueRepository    $estoqueRepository,
                           Request              $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['description']) || !isset($data['unit']) || !isset($data['weight']))
            return $this->json(['message' => "Formulário incorreto"], 422);

        $name = $data['name'];
        $description = $data['description'];
        $unit = $data['unit'];
        $weight = $data['weight'];

        $produto = new Produto($name, $description, $weight, $unit);

        $repository->save($produto, true);

        if(isset($data['fornecedor_id'])){
            $fornecedor = $fornecedorRepository->find([$data['fornecedor_id']]);

            if($fornecedor){
                $produto->addFornecedor($fornecedor);
            }
        }

        if(isset($data['categoria_id'])){
            $categoria = $categoriaRepository->find([$data['categoria_id']]);

            if($categoria){
                $produto->addCategorium($categoria);
            }
        }

        if(isset($data['estoque_id'])){
            $estoque = $estoqueRepository->find([$data['estoque_id']]);

            if($estoque){
                $produtoHasEstoque = new ProdutoHasEstoque($data['quantity'], $produto,  $estoque );
                $produto->addProdutoHasEstoque($produtoHasEstoque);
            }
        }


        return $this->respondCreated(['message' => 'Produto cadastrado com sucesso.']);
    }
    #[Route('/api/produto/{id}', name: "app_produto_update", methods: ["PUT", "PATCH"])]
    public function update(ProdutoRepository    $repository,
                           Request              $request,
                           $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['description']) || !isset($data['unit']) || !isset($data['weight']))
            return $this->json(['message' => "Formulário incorreto"], 422);

        $produto = $repository->find($id);

        if($produto === null) return $this->json(['message' => 'Produto não encontrado'],404);

        $name = $data['name'];
        $description = $data['description'];
        $unit = $data['unit'];
        $weight = $data['weight'];

        $produto->setName($name);
        $produto->setDescription($description);
        $produto->setUnit($unit);
        $produto->setWeight($weight);

        $this->EM->persist($produto);
        $this->EM->flush();

        return $this->respondCreated(['message' => 'Produto atualizado com sucesso.']);
    }



}
