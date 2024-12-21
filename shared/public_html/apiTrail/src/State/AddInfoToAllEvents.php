<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Utilisateurs;
use App\Repository\EventsRepository;
use App\Repository\ParticipationsRepository;
use Symfony\Bundle\SecurityBundle\Security;

class AddInfoToAllEvents implements ProviderInterface
{

    public function __construct(
        private readonly ParticipationsRepository $participationsRepository,
        private readonly Security $security
    )
    {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $allParticipationsOfUser = $this->participationsRepository->getAllFromUserId($uriVariables['idUser'] ?? null);
        $user = $this->security->getUser();
        if ($user instanceof Utilisateurs) {
            foreach ($allParticipationsOfUser as $participation) {
                $event = $participation->getEvent();
                if ($this->participationsRepository->checkIfParticipationExists($user->getId(), $event->getId())) {
                    $event->setParticipating(true);
                } else {
                    $event->setParticipating(false);
                }
                $event->setCountOfParticipation($this->participationsRepository->getCountOfParticipationsByEvent($event->getId()));
            }
        }
        return $allParticipationsOfUser;
    }
}
