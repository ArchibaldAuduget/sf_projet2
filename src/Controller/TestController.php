<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController
{
    public function index() {
        dd("ca fonctionne");
    }

    // public function test() {
        
    //     // Request classe de librairie httpfoundation. Request::createFromGlobals permet de stocker plus simplement les super globals.
    //     // On peut se passer de cette ligne en appelant la méthode de cette manière : public function test(Request $request){}
    //     $request = Request::createFromGlobals();
    //     // $request->query->get permet de récuper $_GET
    //     // On dit ici que $age prend la valeur GET de 'age', et que si elle n'existe pas, elle vaut 0 auto.
    //     $age = $request->query->get('age', 0);
        
    //     // Toutes les fonctions qui prennent en charge des requetes doivent TOUJOURS retourner une instance de la classe Response
    //     return New Response("Vous avez $age ans");
    // }



    public function test(Request $request) {

        // Meme fonction en utilisant les attributs. On peut se passer de la ligne en écrivant : public function test(Request $request, $age).
        // Le parametre doit avoir le même nom que l'attribut dans la route.
        $age = $request->attributes->get('age', 0);
        
        return New Response("Vous avez $age ans");
    }

}