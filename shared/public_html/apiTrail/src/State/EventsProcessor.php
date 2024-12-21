<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Utilisateurs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
class EventsProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface          $persistProcessor,
        private readonly Security $security
    )
    {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $user = $this->security->getUser();
        if ($user instanceof Utilisateurs) {
            $data->setOrganizedBy($user);
        }

        if ($operation->getMethod() === 'POST') {
            $data->setCreatedAt(new \DateTime());
        }

        $data->setUpdatedAt(new \DateTime());

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
