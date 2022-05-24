<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use App\classe\Search;
use App\Form\SearchType;

class ProductController extends AbstractController
{

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine){
        $this->entityManager = $doctrine->getManager();
    }


    #[Route('/nos-produits', name: 'app_product')]
    public function index(Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }else{
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product_item')]
    public function show($slug){

        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);

    }
}
