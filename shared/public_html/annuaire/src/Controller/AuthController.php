<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterForm;
use App\Repository\UserRepository;
use App\Service\FlashMessageHelperInterface;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserManager                 $userManager,
        private readonly FlashMessageHelperInterface $flashMessageHelper,
        private readonly UserRepository              $userRepository
    )
    {
    }

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterForm::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('register')
        ]);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $form['password']->getData();
                $confirmPassword = $form->get('confirmPassword')->getData();

                // Vérifier la confirmation du mot de passe
                if ($plainPassword && $plainPassword !== $confirmPassword) {
                    $form
                        ->get('confirmPassword')
                        ->addError(new FormError('Les mots de passe ne correspondent pas.'));
                } else {
                    $this->userManager->processNewUser($user, $plainPassword);

                    $user->setLastLogin(new \DateTime());
                    $user->setCreatedAt(new \DateTime());
                    $user->setUpdatedAt(new \DateTime());
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->flashMessageHelper->addFormSuccessAsFlash("Bienvenue sur LDS");
                    return $this->redirectToRoute('login');
                }
            }
        }

        $this->flashMessageHelper->addFormErrorsAsFlash($form);

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/login', name: 'login', methods: ['GET', 'POST'])]
    public function connexion(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
        ]);
    }

    #[Route('/check_username/{username}', name: 'check_username', methods: ['GET'])]
    public function checkUsername($username): JsonResponse
    {
        $user = $this->userRepository->getUserByUsername($username);

        if (!empty($user)) {
            return $this->json(['available' => false]);
        }
        return $this->json(['available' => true]);
    }

    #[Route('/check_code/{code}', name: 'check_code', methods: ['GET'])]
    public function checkCode($code)
    {
        if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]+$/', $code)) {
            return $this->json(['available' => false]);
        }

        $user = $this->userRepository->getUserByCode($code);

        if (!empty($user)) {
            return $this->json(['available' => false]);
        }
        return $this->json(['available' => true]);
    }
}
