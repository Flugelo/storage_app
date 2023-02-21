<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $email = $data['email'];
        $name = $data['name'];
        $password = $data['password'];


        if (empty($email) || empty($password) || empty($name)) return $this->json(['message' => 'Email ou senha invalidos'], 401);

        if ($userRepository->findBy(['email' => $email])  != null) return $this->json(['message' => 'Já existe uma conta cadastrada com este email!']);

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $userRepository->save($user, true);


        return $this->json(['message' => 'Usuario registrado com sucesso', 'user' => $user], 200);

    }


    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }
}
