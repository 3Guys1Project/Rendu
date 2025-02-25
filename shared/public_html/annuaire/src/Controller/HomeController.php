<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $cards = $this->userRepository->getCardUsernames($isAdmin);

        return $this->render('home/index.html.twig', [
            'tittle' => 'Home',
            'cards' => $cards
        ]);
    }

    #[Route("/search", name: "search", methods: "POST")]
    public function search(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $query = $data['query'] ?? '';

        $isAdmin = $this->isGranted('ROLE_ADMIN');
        if ($query != '') $results = $this->userRepository->findBySearchQuery($query, $isAdmin);
        else $results = $this->userRepository->getCardUsernames($isAdmin);
        $formattedResults = [];
        foreach ($results as $result) {
            $formattedResults[] = $result;
        }

        return $this->render('components/comment_list.html.twig', [
            'cards' => $formattedResults
        ]);
    }

}
