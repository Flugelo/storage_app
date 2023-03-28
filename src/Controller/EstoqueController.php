<?php

namespace App\Controller;

use App\Entity\Estoque;
use App\Entity\HistoryEstoque;
use App\Repository\ArmazemRepository;
use App\Repository\EstoqueRepository;
use App\Repository\HistoryEstoqueRepository;
use App\Repository\ProdutoRepository;
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
    #[Route('/api/estoque', name: 'app_estoque', methods: ['GET'])]
    public function index(EstoqueRepository $estoqueRepository, Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        $results = null;
        if($search == null){
            $query = $estoqueRepository->createQueryBuilder('e');
            $query = $query->select('e.id','p.name as produto', 'a.name as armazem',  'e.unit_price', 'e.quantity', 'e.qtt_max', 'e.qtt_min')
                ->join('e.produto', 'p')
                ->join('e.armazem', 'a')
                ->orderBy('e.id', 'DESC');

            $results = $query->getQuery()->getResult();
        }else{
            $results = $estoqueRepository->search($search);
        }
        return $this->json($results, 200);
    }
    #[Route('/api/estoque', name: "app_estoque_save", methods: ['POST'])]
    public function save(Request $request, ProdutoRepository $produtoRepository, ArmazemRepository $armazemRepository, EstoqueRepository $estoqueRepository): JsonResponse
    {
        $data = json_decode($request->getContent());

        $produto = $produtoRepository->find($data->produto_id);

        if(!$produto) return $this->json(['message' => 'Produto não encontrado'], 404);

        $armazem = $armazemRepository->find($data->armazem_id);

        if(!$armazem) return $this->json(['message' => 'Armazem não encontrado'], 404);

        $estoque = new Estoque($data->quantity, $produto, $data->qtt_max, $data->qtt_min, $armazem, $data->unit_price);
        $estoque->updateUpdatedAt();

        $estoqueRepository->save($estoque, true);

       return $this->json(['message' => 'Estoque foi cadastrado com sucesso'], 200);
    }

    #[Route('/api/estoque/{id}', name:'app_estoque_show', methods: ['GET'])]
    public function show($id, EstoqueRepository $estoqueRepository): JsonResponse
    {
        $estoque = $estoqueRepository->find($id);

        if(!$estoque) return $this->json(['message' => 'Estoque não encontrado'], 404);

        return $this->json($estoque->getValues(), 200);
    }
    #[Route('/api/estoque/{id}', name: "app_estoque_update", methods: ['PUT', 'PATCH'])]
    public function update($id, EstoqueRepository $estoqueRepository, Request $request): JsonResponse
    {
        $estoque = $estoqueRepository->find($id);

        if(!$estoque) return $this->json(['message' => 'Estoque não encontrado'], 404);

        $data = json_decode($request->getContent());

        if($data->qtt_min != $estoque->getQttMin() ||
            $data->qtt_max != $estoque->getQttMax() ||
            $data->quantity != $estoque->getQuantity() ||
            $data->unit_price != $estoque->getUnitPrice()){
            $historyEstoque = new HistoryEstoque($data->quantity, $data->qtt_max, $data->qtt_min, $data->unit_price, $estoque);
            $historyEstoque->setCreatedAt(new \DateTime());
            $this->EM->getRepository(HistoryEstoque::class)->save($historyEstoque, true);
        }

        $estoque->setQttMin($data->qtt_min);
        $estoque->setQuantity($data->quantity);
        $estoque->setQttMax($data->qtt_max);
        $estoque->setUnitPrice($data->unit_price);

        $this->EM->persist($estoque);
        $this->EM->flush();

        return $this->json(['message' => 'Estoque atualizado com sucesso'], 200);
    }
    #[Route('/api/estoque/history/{estoque_id}', name: 'app_estoque_gethistory', methods: ['GET'])]
    public function getHistor($estoque_id, HistoryEstoqueRepository $historyRepository,
                              EstoqueRepository $estoqueRepository): JsonResponse
    {
        $estoque = $estoqueRepository->find($estoque_id);

        if(!$estoque) return $this->json(['message' => 'Estoque não encontrado'], 404);

        $queryBuilder = $historyRepository->createQueryBuilder('h');
        $queryBuilder = $queryBuilder->select('h.id', 'h.quantity', 'h.qtt_max', 'h.qtt_min', 'h.price_unit', "DATE_FORMAT(h.created_at, '%d/%m/%Y %H:%i') as created_at")
            ->where('h.estoque = :estoque')
            ->setParameter('estoque', $estoque->getId());
        $results = $queryBuilder->getQuery()->getResult();
        return $this->json($results);
    }
}
