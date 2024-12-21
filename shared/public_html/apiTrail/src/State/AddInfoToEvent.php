<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Utilisateurs;
use App\Repository\EventsRepository;
use App\Repository\ParticipationsRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AddInfoToEvent implements ProviderInterface
{

    public function __construct(
        private readonly EventsRepository $eventsRepository,
        private readonly ParticipationsRepository $participationsRepository,
        private readonly Security $security
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|null|object
    {
        $event = $this->eventsRepository->find($uriVariables['id'] ?? null);

        if (!$event) {
            return null;
        }

        $user = $this->security->getUser();
        if ($user instanceof Utilisateurs) {
            if ($this->participationsRepository->checkIfParticipationExists($user->getId(), $event->getId())) {
                $event->setParticipating(true);
            } else {
                $event->setParticipating(false);
            }
        }

        $event->setCountOfParticipation($this->participationsRepository->getCountOfParticipationsByEvent($event->getId()));

        return $event;
    }
}
