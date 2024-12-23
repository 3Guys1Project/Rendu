<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentForm;
use App\Form\ProfileForm;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security               $security,
        private readonly UserManager            $userManager,
        private readonly UserRepository         $userRepository,
        private readonly CommentRepository      $commentRepository,
        private readonly TokenStorageInterface  $tokenStorage

    )
    {
    }

    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(): Response
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('login');
        }

        $profil = $this->userRepository->getAllInformation($user->getCode());
        $comments = $this->commentRepository->getCommentsByUser($user->getCode());
        $avgStars = $this->commentRepository->getAvgStarsByUser($user->getCode());


        return $this->render('profile/profile.html.twig', [
            'profile' => $profil[0],
            'mine' => true,
            'comments' => $comments,
            'avgStars' => $avgStars,
            'canComment' => false
        ]);
    }

    #[Route('/profile/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $user = $this->security->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof User) {
            throw new AccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(ProfileForm::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $confirmPassword = $form->get('confirmPassword')->getData();
            $avatar = $form->get('avatar')->getData();
            $banner = $form->get('banner')->getData();

            // Vérifier la confirmation du mot de passe
            if ($plainPassword && $plainPassword !== $confirmPassword) {
                $form
                    ->get('confirmPassword')
                    ->addError(new FormError('Les mots de passe ne correspondent pas.'));
            } else {
                // Mettre à jour le mot de passe si un nouveau mot de passe est défini
                if ($plainPassword) {
                    $user->setPassword(
                        $this->userManager->encodePassword($user, $plainPassword)
                    );
                }

                //$user->setUpdatedAt(new \DateTime());

                try {
                    $this->userManager->saveAvatar($user, $avatar);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de votre photo de profil.');
                }

                try {
                    $this->userManager->saveBanner($user, $banner);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour de votre bannière.');
                }

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $this->addFlash('success', 'Profil mis à jour avec succès.');

                return $this->redirectToRoute('app_profile');
            }
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'banner' => $user->getBanner(),
            'avatar' => $user->getAvatar()
        ]);
    }

    #[Route('/profile/{code}', name: 'app_profile_by_code', methods: ['GET', 'POST'])]
    public function profileById(Request $request, string $code): Response
    {
        $profil = $this->userRepository->getAllInformation($code);

        if (count($profil) == 0) {
            throw $this->createNotFoundException('User not found');
        }

        $user = $this->security->getUser();
        $isMine = $user && $user->getCode() === $code;

        $comment = new Comment();
        $form = $this->createForm(CommentForm::class, $comment);

        if ($user && !$isMine) {
            $canComment = $this->commentRepository->canComment($user->getCode(), $code);
            if ($canComment) {
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $comment->setSender($user->getCode());
                    $comment->setRecipient($code);

                    $this->entityManager->persist($comment);
                    $this->entityManager->flush();

                    $this->addFlash('success', 'Commentaire ajouté avec succès.');
                }
            }
        }

        $comments = $this->commentRepository->getCommentsByUser($code);
        $avgStars = $this->commentRepository->getAvgStarsByUser($code);
        $canComment = $user && $this->commentRepository->canComment($user->getCode(), $code);

        return $this->render('profile/profile.html.twig', [
            'profile' => $profil[0],
            'mine' => $isMine,
            'form' => $form->createView(),
            'comments' => $comments,
            'avgStars' => $avgStars,
            'canComment' => $canComment
        ]);
    }


    #[Route('/api/profile/{code}', name: 'api_profile_by_id', methods: ['GET'])]
    public function getjSONProfile($code): JsonResponse
    {
        $profil = $this->userRepository->getAllInformation($code);

        if (count($profil) == 0) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($profil[0]);
    }

    #[Route('/account/delete', name: 'app_account_delete', methods: ['POST'])]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->tokenStorage->setToken(null);
        $request->getSession()->invalidate();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/account/delete/{code}', name: 'app_admin_account_delete')]
    public function adminDeleteAccount(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user || !$this->security->isAdmin()) {
            throw $this->createAccessDeniedException('You are not allowed to access this page.');
        }

        $userToDelete = $this->userRepository->findOneBy(['code' => $request->get('code')]);

        if (!$userToDelete) {
            throw $this->createNotFoundException('User not found');
        }

        $entityManager->remove($userToDelete);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
