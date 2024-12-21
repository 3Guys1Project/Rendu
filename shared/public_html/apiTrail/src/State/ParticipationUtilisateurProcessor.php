<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\EventsRepository;
use App\Repository\ParticipationsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

class ParticipationUtilisateurProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        private readonly ParticipationsRepository $participationsRepository,
        private readonly EventsRepository $eventsRepository,
        private readonly Security $security
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $userId = $data->getUser() ? $data->getUser()->getId() : null;
        $eventId = $data->getEvent();

        if ($eventId === null) {
            throw new \Exception('Event not found.');
        }

        $actualUser = $this->security->getUser();

        if ($userId === null) {
            $userId = $actualUser->getId();
        }

        if ($actualUser->getId() !== $userId && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('User not found or not authorized.');
        }

        // Retrieve the event by ID
        $event = $this->eventsRepository->find($eventId);
        if (!$event) {
            throw new \Exception('Event not found.');
        }

        if ($event->getMaxParticipants() - $this->participationsRepository->getCountOfParticipationsByEvent($eventId) <= 0) {
            throw new \Exception('No available slots for this event.');
        }

        if ($this->participationsRepository->checkIfParticipationExists($userId, $eventId)) {
            throw new \Exception('User is already registered for this event.');
        }

        try{
            $this->verifyIfAnyDatesOfAParticipationOverlapWithUs($event, $userId, $eventId);
        } catch (\Exception $e) {
            http_response_code(409);
            echo json_encode(['error' => $e->getMessage(), 'error_code' => 'overlapse']);
            return new Response(null, 409, ['Content-Type' => 'application/json']);
        }

        $data->setUser($actualUser);
        $data->setEvent($event);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

    /**
     * @throws \Exception
     */
    public function verifyIfAnyDatesOfAParticipationOverlapWithUs($event, $userId, $eventId): void
    {
        $userParticipations = $this->participationsRepository->findBy(['user' => $userId]);
        foreach ($userParticipations as $participation) {
            $participatedEvent = $participation->getEvent();
            if ($participatedEvent->getId() === $eventId) {
                continue;
            }
            if (
                $event->getStartAt() <= $participatedEvent->getEndAt() &&
                $event->getEndAt() >= $participatedEvent->getStartAt()
            ) {
                throw new \Exception('Event dates overlap with another event the user is participating in.');
            }
        }
    }
}