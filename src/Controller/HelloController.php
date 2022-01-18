<?php

namespace App\Controller;

use App\Taxes\Calculator;
use App\Taxes\Detector;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController extends AbstractController
{

    #[Route('/hello/{name?world}', name: 'hello')]
    public function hello($name = 'World') {
        return $this->render('hello2.html.twig', [
            'prenom' => $name,
        ]);
    }

    #[Route('/example', name: 'example')]
    public function example() {
        return $this->render('example.html.twig', [
            'age' => 33,
        ]);
    }

}