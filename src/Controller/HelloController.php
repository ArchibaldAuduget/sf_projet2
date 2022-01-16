<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController extends AbstractController
{
    protected $calculator;
    protected $tva;

    // public function __construct(float $tva) {
    //     $this->tva = $tva;
    // }


    public function hello($name, Slugify $slugify, Environment $twig) {
        dump($twig);
        dump($slugify->slugify("Hello World"));
        return New Response("Hello $name");
    }
}