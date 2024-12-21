<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack, private readonly EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
    }

    #[AsEventListener]
    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
            LoginFailureEvent::class => 'onLoginFailure',
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->getFlashBag()->add('success', 'Connexion réussie !');
        $user = $event->getUser();
        $user->setLastLogin(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->getFlashBag()->add('error', 'Login et/ou mot de passe incorrect !');
    }

    public function onLogout(LogoutEvent $event): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->getFlashBag()->add('success', 'Déconnexion réussie !');
    }
}
