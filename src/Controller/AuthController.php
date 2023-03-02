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

class AuthController extends ApiController
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/register', name: 'app_auth_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $request = $this->transformJsonBody($request);
        $email = $request->get("email");
        $username = $request->get("username");
        $name = $request->get("name");
        $password = $request->get("password");


        if (empty($email) || empty($password) || empty($username)) return $this->json(['message' => 'Todos os campos devem ser preenchidos'], 401);

        if ($userRepository->findBy(['email' => $email]) != null) return $this->json(['message' => 'Já existe uma conta cadastrada com este email!'], 409);

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $userRepository->save($user, true);


        return $this->respondWithSuccess(sprintf('Usuário %s foi criado com sucesso.', $user->getName()));
    }


    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
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
