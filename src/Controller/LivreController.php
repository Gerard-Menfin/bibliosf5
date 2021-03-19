<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/biblio")
 * Toutes les routes de ce controleur vont commencer par "/admin"
 */
class LivreController extends AbstractController
{
    /**
     * @Route("/livre", name="livre")
     * @ IsGranted("ROLE_ADMIN")
     */
    public function index(LivreRepository $livreRepository): Response
    {
        $liste_livres = $livreRepository->findAll();
        $livres_indisponibles = $livreRepository->findLivresIndisponibles();
        return $this->render('livre/index.html.twig', compact("liste_livres", "livres_indisponibles"));
    }

    /**
     * @Route("/livre/{id}", name="livre_afficher", requirements={"id"="\d+"})
     */
    public function afficher(Livre $livre)
    {
        return $this->render("livre/afficher.html.twig", compact("livre"));
    }

    /**
     * @Route("/livre/ajouter", name="livre_ajouter")
     */
    public function ajouter(Request $request, EntityManagerInterface $em)
    {
        $livre = new Livre;
        /* Je crée un objet $formLivre avec la méhtode createForm qui va représenter le formulaire généré 
            grâce à la classe LivreType. Ce formulaire est lié à l'objet $livre  */
        $formLivre = $this->createForm(LivreType::class, $livre);
        /* Avec la méthode "handleRequest", le $formLivre va gérer les données qui viennent du formulaire
            On va aussi pouvoir savoir si le formulaire a été soumis et si il est valide */
        $formLivre->handleRequest($request);
        if( $formLivre->isSubmitted()) {
            if( $formLivre->isValid() ){
                $fichier = $formLivre->get("couverture")->getData(); // pour récupérer les informations du fichier uploadé
                if( $fichier ){
                    /* pathinfo: fonction qui permet de récupérer des informations sur un fichier, par exemple le nom du fichier sans le chemin complet, sans l'extension  
                        getClientOriginalName : récupère le nom du fichier uploadé (la méthode est exécuté à partir de l'objet instancié par getData() que l'on a exécuté sur l'objet  formulaire)*/
                    $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                    // $extension = pathinfo($fichier->getClientOriginalName(), PATHINFO_EXTENSION);
                    $nomFichier .= "_" . time();
                    $nomFichier .= "." . $fichier->guessExtension();
                    $nomFichier = str_replace(" ", "_", $nomFichier);
                    $destination = $this->getParameter("dossier_images") . "livres";
                    /* la méthode move va copier le fichier uploadé dans le dossier $destination avec le nom $nomFichier*/
                    $fichier->move($destination, $nomFichier);
                    $livre->setCouverture($nomFichier);
                }
                $em->persist($livre);  // La méthode persist() prépare la requête INSERT INTO à partir de l'objet entity passé en paramètre
                $em->flush();       // La méthode flush() exécute les requêtes en attente
                $this->addFlash("success", "Le nouveau livre a bien été enregistré");
                return $this->redirectToRoute("livre");
            } else {
                $this->addFlash("danger", "Le formulaire n'est pas valide");
            }
        }
        return $this->render("livre/ajouter.html.twig", [ "formLivre" => $formLivre->createView() ]);
    }


    /**
     * @Route("/livre/modifier/{id}", name="livre_modifier", requirements={"id"="\d+"})
     */
    public function modifier(LivreRepository $lr, Request $rq, EntityManagerInterface $em, $id)
    {
        $livre = $lr->find($id);
        $formLivre = $this->createForm(LivreType::class, $livre);
        $formLivre->handleRequest($rq);
        if($formLivre->isSubmitted() && $formLivre->isValid()){
            if( $fichier = $formLivre->get("couverture")->getData() ){
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomFichier .= "_" . time();
                $nomFichier .= "." . $fichier->guessExtension();
                $nomFichier = str_replace(" ", "_", $nomFichier);
                $destination = $this->getParameter("dossier_images") . "livres";
                $fichier->move($destination, $nomFichier);
                
                $ancienFichier = $this->getParameter("dossier_images") . "livres/" . $livre->getCouverture();
                if( file_exists($ancienFichier) &&  $livre->getCouverture()){
                    unlink($ancienFichier);
                }
                $livre->setCouverture($nomFichier);
            }


            // $em->persist($livre);
            // Dès qu'un objet entity a un id non null, l'EntityManager va mettre la bdd à jour 
            // avec les informations de cet objet quand la méthode flush() sera exécutée
            $em->flush();
            return $this->redirectToRoute("livre");
        }
        return $this->render("livre/ajouter.html.twig", [ "formLivre" => $formLivre->createView() ]);
    }

    /**
     * @Route("/livre/supprimer/{id}", name="livre_supprimer", requirements={"id"="\d+"})
     */
    public function supprimer(Request $rq, EntityManagerInterface $em, Livre $livre)
    {
        if( $rq->isMethod("POST") ){
            $nomFichier = $livre->getCouverture();
            $em->remove($livre);  // La méthode remove() prépare une requête DELETE
            $em->flush();

            $ancienFichier = $this->getParameter("dossier_images") . "livres/" . $nomFichier;
            if( file_exists($ancienFichier) &&  $nomFichier){
                unlink($ancienFichier);
            }
            $this->addFlash("success", "Le livre a bien été supprimé");
            return $this->redirectToRoute("livre");
        }    

        return $this->render("livre/supprimer.html.twig", compact("livre"));
    }

    // EXO : Route pour afficher un livre : livre_afficher
    // ajouter les liens sur la liste des livres
}
