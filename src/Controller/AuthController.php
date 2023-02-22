<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

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

        if ($userRepository->findBy(['email' => $email]) != null) return $this->json(['message' => 'Já existe uma conta cadastrada com este email!']);

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

    #[Route('/api/valid_token', name: 'app_auth_isvalid', methods: ['GET'])]
    public function isValid(Request $request): JsonResponse
    {
        $bearer = $request->headers->get('Authorization');

        if($bearer === null) return $this->json(['message' => 'token inexistente'], 401);

        try{
            $token =  str_replace(['Bearer', ' '], '', $bearer);
            $tokenParts = explode(".", $token);
            $tokenPayload = base64_decode($tokenParts[1]);
            $jwtPayload = json_decode($tokenPayload);

        }catch (\Exception $e){
            return $this->json(['message' => 'Token invalido'], 401);
        }

        if (time() > $jwtPayload->exp) {
            // O token expirou
            return $this->json(['message' => 'Token expirado'], 401);
        }

        return $this->json(['message' => 'Token válido'], 200);
    }
}
