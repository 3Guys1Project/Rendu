<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Utilisateurs;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateursProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface          $persistProcessor,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Utilisateurs && $this->legitRoleChecker($data->getRoles())) {

            if ($data->getPlainPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPlainPassword());
                $data->setHash($hashedPassword);
                $data->eraseCredentials();
            }

            if ($operation->getMethod() === 'POST') {
                $data->setCreatedAt(new \DateTime());
            }

            $data->setUpdatedAt(new \DateTime());


        } else {
            if (in_array('ROLE_ADMIN', $data->getRoles())) {
                throw new \Exception('You cannot set the role ROLE_ADMIN.');
            }
            throw new \Exception('User not found or not authorized or role doesnt exist.');
        }
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);

    }

    protected function legitRoleChecker($roles): bool
    {
        if (empty($roles)) return false;
        if (in_array('ROLE_ADMIN', $roles)) return false;
        foreach ($roles as $role) {
            if (!in_array($role, ['ROLE_USER', 'ROLE_ORGA', 'ROLE_ADMIN'])) {
                return false;
            }
        }
        return true;
    }
}