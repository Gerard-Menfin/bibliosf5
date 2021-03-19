<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     * 
     * Une route est une correspondance entre une URL et une méthode d'un contrôleur
     * Par exemple, quand dans la barre d'adresse du navigateur, après le 'nom de domaine',
     * il y a "/test", la méthode qui sera exécuté sera la méthode index() de TestController
     * Symfony utilise les annotations pour définir les routes. 
     * La fonction @Route a un paramètre obligatoire : l'url relative de la route
     * 
     * Affichage de la page test
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    /**
     * @Route("/test/calcul/{b}/{a}", requirements={"b"="\d+", "a"="[0-9]+"})
     * 
     * J'utilise les expressions régulières (regex) pour obliger à ce que les paramètres de la route soit 
     * uniquement composés de chiffres :
     *          \d ou [0-9]     veut dire un caractère numérique 
     *          +               veut dire que le caractère précédent doit être présent au moins 1 fois
     */
    public function calcul($b, $a){
        // $a = 5;
        // $b = 6;
        $resultat = $a + $b;

        // return $this->json([
        //     "calcul" => "$a + $b",
        //     "resultat" => $resultat
        // ]);

        /*
            La méthode render construit l'affichage. Le 1er paramètre est le nom de la vue à utiliser.
            Le nom de la vue est le chemin du fichier à partir du dossier "templates"
        */
        return $this->render("test/calcul.html.twig", [ 
            "result" => $resultat,
            "calcul" => "$a + $b",
            "a" => $a,
            "b" => $b
        ]);
        /* EXO: 1.affichez le resultat du calcul 5 + 6 est égal à 11 */
        /*      2.modifiez la route et la méthode pour que la valeur $b soit récupéré dans l'url
                3. La route "calcul" ne doit plus utiliser "base.html.twig" pour son affichage
                   Et si "base.html.twig" est utilisé dans un render, on ne doit pas être obligé 
                   d'envoyer des variables pour qu'elle s'affiche
        */
    }

    /**
     * @Route("/test/salutations-distinguees/{prenom}")
     * Une route paramétrée permet de récupérer une valeur dans l'URL. L'URL n'est pas fixe, la valeur du paramètre
     * peut changer
     */
    public function salut($prenom)
    {
        // $prenom = "Jean";
        return $this->render("test/salut.html.twig", [ "prenom" => $prenom ]);
    }

    /**
     * @Route("/test/tableau")
     */
    public function tableau()
    {
        $tab = [ "nom" => "Cérien", "prenom" => "Jean" ];
        return $this->render("test/tableau.html.twig", [ 
            "tableau" => $tab 
        ]);
    }

    /**
     * @Route("/test/objet")
     */
    public function objet()
    {
        $objet = new \stdClass;
        $objet->nom = "Mentor";
        $objet->prenom = "Gérard";
        return $this->render("test/tableau.html.twig", [ 
            "tableau" => $objet 
        ]);
    }

    /**
     * @Route("/test/boucles")
     */
    public function boucles()
    {
        $tableau = [ "bonjour", "je", "suis", "en", "cours", "de", "Symfony"];
        $chiffres = [];
        for($i = 0; $i < 10; $i++){
            $chiffres[] = $i * 12;
        }
        
        return $this->render("test/boucles.html.twig", [
            "chiffres" => $chiffres,
            "tableau" => $tableau
        ]);
    }

    /**
     * @Route("/test/condition")
     */
    public function condition()
    {
        $a = 12;
        return $this->render("test/condition.html.twig", [
            "a" => $a,
            // "b" => ""
        ]);
    }

    /* EXO : 1.créer un controleur AccueilController avec une route qui va afficher "La bibliothèque est vide pour l'instant"
             2. la route doit correspondre à la racine du site

             3. Dans le contrôleur Test, ajouter 2 routes : 
                une route (/test/affiche-formulaire) qui affiche un formulaire html (POST) 
                l'autre (/test/affiche-donnees) qui affiche les données tapées dans ce formulaire (avec $_POST)
    */

    /**
     * @Route("/test/affiche-formulaire", name="test_affiche_formulaire")
     */
    public function formulaire(){
        return $this->render("test/formulaire.html.twig");
    }

    /**
     * @Route("/test/donnees", name="test_affiche_donnees")
     */
    public function donnees()
    {
        if( $_POST ){  // condition : si $_POST n'est pas vide
            extract($_POST); // extract() va créer autant de variables qu'il y a d'indices dans un tableau associatif

            /* compact() retourne un array associatif qui sera formé ainsi : 
                compact("pseudo", "mdp")  ... [ "pseudo" => $pseudo, "mdp" => $mdp ];
                chaque paramètre passé à la fonction compact va créer un indice et la
                valeur sera la variable qui a le même nom
                */
            return $this->render("test/donnees.html.twig", compact("pseudo", "mdp"));
        }
    }

    /**
     * @Route("/test/affiche-donnees", name="test_donnees")
     * 
     * On ne peut pas instancier un objet de la classe Request, donc pour pouvoir l'utiliser, on va utiliser ce qu'on appelle l'injection de dépendance (vous verrez aussi parfois : autowiring) : en passant par les 
     * paramètres d'une méthode d'un contrôleur, l'objet de la classe est 
     * automatiquement instancié et remplit (si besoin)
     * Les classes que l'on peut utiliser avec l'injection de dépendances sont
     * appelés des services (dans Symfony)
     * 
     * La classe Request contient toutes les valeurs des variables superglobales de PHP, et quelques infos supplémentaires concernant la requête HTTP 
     */
    public function afficheDonnees(Request $request){
        dump($request);
        //dd($request); // Dump and Die : arrête l'exécution du php après le var_dump
        if( $request->isMethod("POST") ){
            $pseudo = $request->request->get("pseudo"); // L'objet $request a une propriété 'request' qui contient $_POST
                                                        // Cette propriété est un objet qui a une méthode get() pour récupérer
                                                        // une valeur
                                                        // Pour le contenu de $_GET, on utilisera, de la même façon, la propriété
                                                        // 'query' de l'objet $request
            $mdp = $request->request->get("mdp");
            return $this->render("test/donnees.html.twig", compact("pseudo", "mdp"));
        }
    }

    /**
     * @Route("/test/find")
     */
    public function testfind(LivreRepository $lr)
    {
        $livres = $lr->findBy([ "titre" => "Dune" ]);
        dd($livres);
    }
}
