<?php

namespace App\Controller;

use App\Entity\HistoryEstoque;
use App\Entity\Ordem;
use App\Entity\Output;
use App\Repository\EstoqueRepository;
use App\Repository\OrderRepository;
use App\Repository\OutputRepository;
use App\Repository\ProdutoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OutputController extends AbstractController
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }
    #[Route('/api/output', name: 'app_output', methods: ['GET'])]
    public function index(OutputRepository $outputRepository): JsonResponse
    {
        $queryBuilder = $outputRepository->createQueryBuilder('o');
        $queryBuilder = $queryBuilder->select('o.id','o.total', 'o.subtotal', 'o.discount', 'o.payment_method' ,"DATE_FORMAT(o.created_at, '%d/%m/%Y %H:%i') as created_at")->orderBy('o.id', 'DESC');;
        $result = $queryBuilder->getQuery()->getResult();

        return $this->json($result, 200);
    }

    #[Route('/api/output/{id}', name: 'app_output_show', methods: ['GET'])]
    public function show($id, OutputRepository $outputRepository): JsonResponse
    {
        $output = $outputRepository->find($id);

        if(!$output) return $this->json(['message' => 'Saída não encontrada'], 404);

        return $this->json($output->getValues(),200);


    }

    #[Route('/api/output', name: 'app_output_create', methods: ['POST'])]
    public function create(Request $request,
                           OutputRepository $outputRepository,
                           OrderRepository $orderRepository,
                           ProdutoRepository $produtoRepository,
                           EstoqueRepository $estoqueRepository): ?JsonResponse
    {
        $data = json_decode($request->getContent());
        if($data->produtos === null) return $this->json(['message' => 'Produtos não encontrados na saida'], 404);

        $output = new Output($data->subtotal, $data->total, $data->discount, "Dinheiro");
        $output->setCreatedAt(new \DateTime());
        $output->setUpdatedAt(new \DateTime());
        $outputRepository->save($output);

        foreach ($data->produtos as $produto) {
            $estoque = $estoqueRepository->find($produto->estoque_id);

            if(!$estoque) return $this->json(['message' => 'Estoque não encontrado'], 404);

            $findProduct = $produtoRepository->find($produto->produto_id);

            if(!$findProduct) return $this->json(['message' => 'Produto não encontrado'], 404);

            $order = new Ordem($produto->price, $produto->qtt_sale, $estoque);
            $order->setProduto($findProduct);
            $estoque->subtractQuantity($produto->qtt_sale);

            $historyEstoque = new HistoryEstoque($estoque->getQuantity(), $estoque->getQttMax(), $estoque->getQttMin(), $estoque->getUnitPrice(), $estoque);
            $historyEstoque->setCreatedAt(new \DateTime());
            $this->EM->getRepository(HistoryEstoque::class)->save($historyEstoque, true);

            $order->setCreatedAt(new \DateTime());
            $order->setOutput($output);
            $orderRepository->save($order, true);

            $this->EM->flush();

        }


        return $this->json(['message' => 'Venda salva com sucesso.'], 200);
    }


}
