<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Fornecedor;
use App\Entity\Produto;
use App\Repository\CategoriaRepository;
use App\Repository\FornecedorRepository;
use App\Repository\ProdutoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        $queryBuilder = $queryBuilder->select('p.id', 'p.name', 'p.description', 'p.unit', 'p.weight', 'p.created_at')->orderBy('p.id', 'DESC');;

        $result = $queryBuilder->getQuery()->getResult();

        return $this->json($result, 200);
    }

    #[Route('/api/produto/{id}', name: 'app_produto_show', methods: ["GET"])]
    public function show(ProdutoRepository $repository, $id): JsonResponse
    {
        $produto = $repository->find($id);

        if ($produto === null) return $this->json(["message" => "Produto não encontrado"], 404);

        return $this->json($produto->getValue(), 200);
    }

    #[Route('/api/produto', name: "app_produto_create", methods: ["POST"])]
    public function create(ProdutoRepository $repository,
                           Request           $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['description']) || !isset($data['unit']) || !isset($data['weight']))
            return $this->json(['message' => "Formulário incorreto"], 422);

        $name = $data['name'];
        $description = $data['description'];
        $unit = $data['unit'];
        $weight = $data['weight'];

        $produto = new Produto($name, $description, $weight, $unit);
        $produto->setCreatedAt(new \DateTime());
        $produto->setUpdatedAt(new \DateTime());

        $repository->save($produto, true);
        return $this->respondCreated(['message' => 'Produto cadastrado com sucesso.']);
    }

    #[Route('/api/produto/{id}', name: "app_produto_update", methods: ["PUT", "PATCH"])]
    public function update(ProdutoRepository $repository,
                           Request           $request,
                                             $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['description']) || !isset($data['unit']) || !isset($data['weight']))
            return $this->json(['message' => "Formulário incorreto"], 422);

        $produto = $repository->find($id);

        if ($produto === null) return $this->json(['message' => 'Produto não encontrado'], 404);

        $name = $data['name'];
        $description = $data['description'];
        $unit = $data['unit'];
        $weight = $data['weight'];

        $produto->setName($name);
        $produto->setDescription($description);
        $produto->setUnit($unit);
        $produto->setWeight($weight);
        $produto->setUpdatedAt(new \DateTime());

        $this->EM->persist($produto);
        $this->EM->flush();

        return $this->respondCreated(['message' => 'Produto atualizado com sucesso.']);
    }

    #[Route('/api/produto/link/fornecedor/{produto_id}/{fornecedor_id}', name: "api_produto_link_fornecedor", methods: ['GET'])]
    public function linkToFornecedor($produto_id, $fornecedor_id, ProdutoRepository $produtoRepository, FornecedorRepository $fornecedorRepository): JsonResponse
    {
        $fornecedor = $fornecedorRepository->find($fornecedor_id);

        if (!$fornecedor) return $this->json(['message' => "Fornecedor não encontrado"], 404);


        $produto = $produtoRepository->find($produto_id);

        if (!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);

        if ($fornecedor->getProdutos()->contains($produto)) return $this->json(['message' => 'Produto já esta vinculado a esse fornecedor'], 409);

        $produto->setUpdatedAt(new \DateTime());
        $produto->addFornecedor($fornecedor);
        $this->EM->flush();

        return $this->json(['message' => 'Vinculo feito com sucesso.'], 200);
    }

    #[Route('/api/produto/unlink/fornecedor/{produto_id}/{fornecedor_id}', name: "api_produto_unlink_fornecedor", methods: ['GET'])]
    public function unLinkToFornecedor($produto_id, $fornecedor_id, ProdutoRepository $produtoRepository, FornecedorRepository $fornecedorRepository): JsonResponse
    {
        $fornecedor = $fornecedorRepository->find($fornecedor_id);

        if (!$fornecedor) return $this->json(['message' => "Fornecedor não encontrado"], 404);

        $produto = $produtoRepository->find($produto_id);

        if (!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);

        if (!$fornecedor->getProdutos()->contains($produto)) return $this->json(['message' => 'Este produto não é vinculado a esse fornecedor'], 409);

        $produto->setUpdatedAt(new \DateTime());
        if($produto->getFornecedor()->contains($fornecedor)){
            $produto->removeFornecedor($fornecedor);
            $this->EM->flush();
        }
        return $this->json(['message' => 'Fornecedor desvinculado feito com sucesso.'], 200);
    }

    #[Route('/api/produto/getUnLinkFornecedores/{produto_id}', name: "api_produto_get_unlink_fornecedor", methods: ['GET'])]
    public function getUnLinkFornecedores($produto_id, ProdutoRepository $repository): JsonResponse
    {
        $produto = $repository->find($produto_id);

        if (!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);

        $queryBuilder = $repository->createQueryBuilder('p');
        $queryBuilder->select('f.id')->join('p.fornecedor', 'f')->where('p.id = :id')->setParameter('id', $produto_id);

        $fornecedores = $queryBuilder->getQuery()->getResult();

        $query = $this->EM->getRepository(Fornecedor::class)->createQueryBuilder('f');

        if (count($fornecedores) != 0) {
            $query->select('f.id', 'f.fantasia')
                ->where('f.id NOT IN (:fornecedores)')
                ->andWhere('f.status = :status')
                ->setParameter('fornecedores', $fornecedores)
                ->setParameter('status', 1);
        } else $query->select('f.id', 'f.fantasia')->where('f.status = :status')->setParameter('status', 1);
        $result = $query->getQuery()->getResult();

        return $this->json($result, 200);

    }

    #[Route('/api/produto/unlink/categoria/{produto_id}/{categoria_id}', name: "api_produto_unlink_categoria", methods: ['GET'])]
    public function unLinkCategoria($produto_id, $categoria_id, ProdutoRepository $produtoRepository, CategoriaRepository $categoriaRepository): JsonResponse
    {
        $produto = $produtoRepository->find($produto_id);

        if (!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);

        $categoria = $categoriaRepository->find($categoria_id);

        if(!$categoria) return $this->json(['message' => 'Categoria não encontrado'], 404);

        $produto->setUpdatedAt(new \DateTime());
        if($produto->getCategoria()->contains($categoria)){
            $produto->removeCategorium($categoria);
            $this->EM->flush();
        }

        return $this->json(['message' => 'Categoria desvinculada com sucesso'], 200);

    }

    #[Route('/api/produto/link/categoria/{produto_id}/{categoria_id}', name: "api_produto_link_categoria", methods: ['GET'])]
    public function linkToCategoria($produto_id, $categoria_id, ProdutoRepository $produtoRepository, CategoriaRepository $categoriaRepository): JsonResponse
    {
        $produto = $produtoRepository->find($produto_id);

        if (!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);

        $categoria = $categoriaRepository->find($categoria_id);

        if(!$categoria) return $this->json(['message' => 'Categoria não encontrado'], 404);

        $produto->setUpdatedAt(new \DateTime());
        if(!$produto->getCategoria()->contains($categoria)){
            $produto->addCategorium($categoria);
            $this->EM->flush();
        }
        return $this->json(['message' => 'Categoria vinculada com sucesso'], 200);

    }

    #[Route('/api/produto/getUnLinkCategoria/{produto_id}', name: "api_produto_get_unlink_categoria", methods: ['GET'])]
    public function getUnLinkCategoria($produto_id, ProdutoRepository $repository): JsonResponse
    {
        $produto = $repository->find($produto_id);

        if (!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);

        $queryBuilder = $repository->createQueryBuilder('p');
        $queryBuilder->select('c.id')->join('p.categoria', 'c')->where('p.id = :id')->setParameter('id', $produto_id);

        $categoria = $queryBuilder->getQuery()->getResult();

        $query = $this->EM->getRepository(Categoria::class)->createQueryBuilder('c');
        if (count($categoria) != 0) {
            $query->select('c.id', 'c.name')
                ->where('c.id NOT IN (:categoria)')
                ->setParameter('categoria', $categoria);
        } else $query->select('c.id', 'c.name');
        $result = $query->getQuery()->getResult();

        return $this->json($result, 200);

    }


}
