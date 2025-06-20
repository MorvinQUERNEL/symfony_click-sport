<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductsRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductsRepository $productsRepository): Response
    {
        $latestProducts = $productsRepository->findBy([], ['createdAt' => 'DESC'], 5);
        return $this->render('home/index.html.twig', [
            'latestProducts' => $latestProducts
        ]);
    }
} 