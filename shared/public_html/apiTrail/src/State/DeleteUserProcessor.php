<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Utilisateurs;
use App\Repository\RefreshTokenRepository;
use App\Repository\UtilisateursRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;


class DeleteUserProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface     $persistProcessor,
        private readonly RefreshTokenRepository $refreshTokenRepository,
        private readonly UtilisateursRepository $utilisateursRepository
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Utilisateurs) {
            throw new \Exception('User not valid.');
        }

        $tokens = $this->refreshTokenRepository->findByUserLogin(['login' => $data->getLogin()]);
        foreach ($tokens as $token) {
            $this->refreshTokenRepository->deleteById($token);
        }

        $this->utilisateursRepository->deleteUser($data->getId());

        foreach($_COOKIE as $cookie_name => $cookie_value){

            unset($_COOKIE[$cookie_name]);
            setcookie($cookie_name, null, -1, '/');
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}