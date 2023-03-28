<?php

namespace App\Controller;

use App\Repository\OutputRepository;
use App\Repository\ShoppingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/{reactRouting}", name="index", priority="-1", defaults={"reactRouting": null}, requirements={"reactRouting"=".+"})
     */
    public function index(): JsonResponse
    {
        return $this->json("Salve");
    }
    #[Route('/api/commerce/graph', name: 'app_home_commerce_graph', methods: ['GET'])]
    public function graph(OutputRepository $outputRepository, ShoppingRepository $shoppingRepository): JsonResponse
    {
        $query = $outputRepository->createQueryBuilder('o');
        $query = $query->select('o.id', 'SUM(o.total) total', "DATE_FORMAT(o.created_at, '%d/%m') as created_at")
            ->where('MONTH(o.created_at) = MONTH(now())')
            ->groupBy('created_at');
        $vendas = $query->getQuery()->getResult();

        $query = $shoppingRepository->createQueryBuilder('s');
        $query = $query->select('s.id', 'SUM(s.total_price) total', "DATE_FORMAT(s.created_at, '%d/%m') as created_at")
            ->where('MONTH(s.created_at) = MONTH(now())')
            ->where('s.status = :status')
            ->setParameter('status', 2)
            ->groupBy('created_at');
        $compras = $query->getQuery()->getResult();

        return $this->json(['vendas' => $vendas, 'compras' => $compras],200);
    }

    #[Route('/api/commerce/sales', name: 'app_home_commerce_sales', methods: ['GET'])]
    public function sales(OutputRepository $repository): JsonResponse
    {
        $query = $repository->createQueryBuilder('o');
        $query = $query->select('o.id', 'o.total', "DATE_FORMAT(o.created_at, '%d/%m %H:%i') as created_at")
            ->where('MONTH(o.created_at) = MONTH(now())')
            ->orderBy('o.id', 'DESC');
        $results = $query->getQuery()->getResult();

        return $this->json($results,200);
    }

    #[Route('/api/commerce/output', name: 'app_home_valueoutput', methods: ['GET'])]
    public function valueOutPut(OutputRepository $outputRepository, ShoppingRepository $shoppingRepository): JsonResponse
    {
        $query = $outputRepository->createQueryBuilder('o');
        $query = $query->select('SUM(o.total) as total')
            ->where('MONTH(o.created_at) = MONTH(now())');
        $venda = $query->getQuery()->getResult();

        $query = $shoppingRepository->createQueryBuilder('s');
        $query = $query->select('SUM(s.total_price) as total')
            ->where('MONTH(s.created_at) = MONTH(now())')
            ->where('s.status = :status')
            ->setParameter('status', 2);

        $compras = $query->getQuery()->getResult();

        return $this->json(['vendas' => $venda[0], 'compras' => $compras[0]],200);
    }
}
