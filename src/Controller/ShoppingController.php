<?php

namespace App\Controller;

use App\Entity\HistoryEstoque;
use App\Entity\ProdutoShopping;
use App\Entity\Shopping;
use App\Repository\ArmazemRepository;
use App\Repository\EstoqueRepository;
use App\Repository\HistoryEstoqueRepository;
use App\Repository\ProdutoRepository;
use App\Repository\ProdutoShoppingRepository;
use App\Repository\ShoppingRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingController extends AbstractController
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }

    #[Route('/api/shopping', name: 'app_shopping', methods: ['GET'])]
    public function index(ShoppingRepository $shoppingRepository): JsonResponse
    {
        $query = $shoppingRepository->createQueryBuilder('shop');
        $query = $query->select('shop.id', 'shop.status',
            "DATE_FORMAT(shop.expected_date, '%d/%m/%Y') as expected_date", 'shop.description', 'shop.total_price',
            "DATE_FORMAT(shop.created_at, '%d/%m/%y') as created_at")
            ->orderBy('shop.id', 'DESC');
        $results = $query->getQuery()->getResult();

        return $this->json($results, 200);
    }

    #[Route('/api/shopping', name: 'app_shopping_create', methods: ['POST'])]
    public function create(Request                   $request,
                           ProdutoRepository         $produtoRepository,
                           ArmazemRepository         $armazemRepository,
                           EstoqueRepository $estoqueRepository): JsonResponse
    {
        $data = json_decode($request->getContent());
        if (!isset($data->produtos) || $data->expected_date === null || $data->status === null) return $this->json(['message' => 'Fumulário incorreto'], 422);

        $shopping = new Shopping($data->status, new \DateTime($data->expected_date), $data->description, $data->total_price);
        $shopping->setCreatedAt(new \DateTime());
        $shopping->setUpdatedAt(new \DateTime());

        foreach ($data->produtos as $produto) {
            $findProduct = $produtoRepository->find($produto->id);

            if ($findProduct === null) return $this->json(['message' => 'Produto ' . $produto->name . ' não encontrado'], 404);

            $estoque = $estoqueRepository->find($produto->estoque->estoqueId);

            if ($estoque === null) return $this->json(['message' => 'Estoque não encontrado'], 404);

            $produtoShopping = new ProdutoShopping($produto->quantity, $findProduct, $estoque, $produto->price);
            $shopping->addProdutoShopping($produtoShopping);

            $this->EM->persist($shopping);
            $this->EM->persist($produtoShopping);
            $this->EM->flush();

        }


        return $this->json(['message' => 'Compra cadastrada com sucesso'], 200);
    }

    #[Route('/api/shopping/{id}', name: 'app_shopping_show', methods: ['GET'])]
    public function show($id, ShoppingRepository $shoppingRepository): JsonResponse
    {
        $shopping = $shoppingRepository->find($id);

        if ($shopping === null) return $this->json(['message' => 'Compra não encontrada'], 404);

        return $this->json($shopping->getValues());
    }

    #[Route('/api/shopping/{id}', name: 'app_shopping_update', methods: ['PUT', 'PATCH'])]
    public function update($id, ShoppingRepository $shoppingRepository,
                           EstoqueRepository $estoqueRepository,
                           HistoryEstoqueRepository $historyEstoqueRepository,
                           Request $request): JsonResponse
    {
        $shopping = $shoppingRepository->find($id);

        if (!$shopping) return $this->json(['message' => 'Compra não encontrada', 404]);

        if($shopping->getStatus() === 2 || $shopping->getStatus() === 3) return $this->json(['message' => 'Não foi possivel atualizar essa compra, pois a mesma ja foi finalizada.'], 302);

        $data = json_decode($request->getContent());

        $shopping->setUpdatedAt(new \DateTime());
        $shopping->setStatus($data->status);
        $shopping->setDescription($data->description);
//        foreach ($shopping->getProdutoShopping() as $produto){
//            return $this->json($produto->getProduto()->getValue());
//        }

        if ($data->status === "2") {
            foreach ($shopping->getProdutoShopping() as $produtoShopping) {

                $estoque = $estoqueRepository->find($produtoShopping->getEstoque());

                if ($estoque !== null) {

                    $estoque->setQuantity($estoque->getQuantity() + $produtoShopping->getQttProduct());
                    $estoque->updateUpdatedAt();

                    $historyEstoque = new HistoryEstoque($estoque->getQuantity(), $estoque->getQttMax(), $estoque->getQttMin(), $estoque->getUnitPrice(), $estoque);
                    $historyEstoque->setCreatedAt(new \DateTime());

                    $historyEstoqueRepository->save($historyEstoque);

                    $this->EM->persist($estoque);
                    $this->EM->flush();

                }else {
                    return $this->json(['message' => 'Estoque não encontrado'],404);
                }
            }
        }

        $this->EM->persist($shopping);
        $this->EM->flush();
        return $this->json([
            'message' => 'Compra atualizada com sucesso',
        ], 200);
    }
}
