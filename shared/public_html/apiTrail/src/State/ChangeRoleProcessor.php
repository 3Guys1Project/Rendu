<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ChangeRoleProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $validRoles = ['ROLE_USER', 'ROLE_ORGA', 'ROLE_ADMIN'];
        foreach ($data->getRoles() as $role) {
            if (!in_array($role, $validRoles, true)) {
                throw new \Exception('Role not found.');
            }
        }
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
