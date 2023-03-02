<?php

namespace App\Controller;

use App\Entity\Contato;
use App\Repository\ContatoRepository;
use App\Repository\FornecedorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class ContatoController extends AbstractController
{
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }

    #[Route('/api/contato/{fornecedor_id}', name: 'app_contato', methods: ["POST"])]
    public function save( Request $request, ContatoRepository $contatoRepository, FornecedorRepository $fornecedorRepository, $fornecedor_id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $titulo = $data['titulo'];
        $telefone = $data['telefone'];
        $email = $data['email'];

        if($titulo == null || $telefone == null || $email == null) return $this->json(['message' => 'formulário incorreto.'], 422);

        $fornecedor = $fornecedorRepository->find($fornecedor_id);

        if($fornecedor === null) return $this->json(['message' => 'Fornecedor não encontrado.'], 422);

        $contato = new Contato($titulo, $telefone, $email);
        $fornecedor->addContato($contato);;

        $contatoRepository->save($contato, true);
        return $this->json(['message' => 'Contato salvo com sucesso'], 200);

    }
    #[Route('/api/contato/{id}', name: "app_contato_show", methods: ["GET"])]
    public function show(ContatoRepository $repository,$id): JsonResponse{

        $contato = $repository->find($id);

        if($contato === null) return $this->json(["message" => "Contato não encontrado"],200);

        $contato = $contato->getVelues();

        return $this->json(["contato" => $contato],200);
    }

    #[Route('/api/contato/{id}', name: 'app_contato_update', methods: ["PUT", "PATCH"])]
    public function update( Request $request, ContatoRepository $contatoRepository, FornecedorRepository $fornecedorRepository, $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $titulo = $data['titulo'];
        $telefone = $data['telefone'];
        $email = $data['email'];

        if($titulo == null || $telefone == null || $email == null) return $this->json(['message' => 'formulário incorreto.'], 422);

        $contato = $contatoRepository->find($id);

        if($contato === null) return $this->json(['message' => 'Contato não encontrado.'], 404);

        $contato->setEmail($email);
        $contato->setTelefone($telefone);
        $contato->setTitulo($titulo);
        $contatoRepository->save($contato, true);

        return $this->json(['message' => 'Contato atualizado com sucesso'], 200);

    }

#[Route('/api/contato/{id}', name: "app_contato_delete", methods: ['DELETE'])]
    public function delete(ContatoRepository $repository, FornecedorRepository $fornecedorRepository, $id){
    $contato = $repository->find($id);

    if($contato === null) return $this->json(['message' => 'Contato não encontrado.'], 404);

    $fornecedor = $contato->getFornecedor();
    $fornecedor->removeContato($contato);
    $repository->remove($contato, true);

    return $this->json(['message' => 'Contato deletado com sucesso'], 200);

    }
}
