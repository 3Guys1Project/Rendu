<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\EventsRepository;
use App\Repository\ParticipationsRepository;

class AddCountsToEvents implements ProviderInterface
{

    public function __construct(
        private readonly ParticipationsRepository $participationsRepository,
        private readonly EventsRepository $eventsRepository,
    )
    {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $eventids = $this->eventsRepository->findAll();

        foreach ($eventids as $event) {
            $event->setCountOfParticipation($this->participationsRepository->getCountOfParticipationsByEvent($event->getId()));
        }

        return $eventids;
    }
}
