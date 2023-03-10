<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->EM = $doctrine->getManager();
    }

    #[Route('/api/users', name: 'app_user', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $query = $userRepository->createQueryBuilder('u');
        $query->select('u.id', 'u.name', 'u.username', 'u.email', 'u.created_at');
        $result = $query->getQuery()->getResult();

        return $this->json($result, 200);
    }

    #[Route('/api/users/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show($id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);
        return $this->json($user->getValue(), 200);
    }

    #[Route('/api/users/{id}', name: "app_user_update", methods: ['PUT', 'PATCH'])]
    public function update($id, Request $request): JsonResponse
    {
        $user = $this->EM->find(User::class, $id);

        if (!$user) return $this->json(['message' => 'Não foi possivel encontrar o usuario solicitado'], 404);

        $data = json_decode(
            $request->getContent(),
            true
        );

        $email = $data['email'];
        $name = $data['name'];
        $sobrenome = $data['sobrenome'];


        $user->setName($name);
        $user->setSobreNome($sobrenome);
        $user->setEmail($email);
        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->EM->persist($user);
        $this->EM->flush();


        return $this->json(['message' => 'Usuário atualizado com sucesso.'], 200);

    }

    #[Route('api/users/{id}', name: "app_user_delete", methods: ['DELETE'])]
    public function delete($id): JsonResponse
    {
        $user = $this->EM->find(User::class, $id);

        if (!$user) return $this->json(['message' => 'Não foi possivel encontrar o usuario solicitado'], 404);

        $this->EM->remove($user);
        $this->EM->flush();

        return $this->json(['message' => 'Usuário deletado com sucesso.'], 200);
    }


}
