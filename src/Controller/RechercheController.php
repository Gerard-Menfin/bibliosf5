<?php

namespace App\Controller;

use App\Form\RechercheType;
use App\Repository\AbonneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\Request;

class RechercheController extends AbstractController
{
    /**
     * @Route("/recherche", name="recherche")
     */
    public function index(Request $rq, LivreRepository $lr): Response
    {
        /*
            Un objet de la classe Request a des propriétés qui contiennent les valeurs des superglobales
            $rq->query   : $_GET
            $rq->request : $_POST
            $rq->cookies : $_COOKIE ...
        */
        $mot = $rq->query->get("mot");
        
        $livres = $lr->recherche($mot);
        $livres_indisponibles = $lr->findLivresIndisponibles();
        return $this->render('recherche/index.html.twig', compact("livres", "mot", "livres_indisponibles"));
    }

    /**
     * @Route("/recherche/globale", name="recherche_globale")
     */
    public function recherche(Request $rq, LivreRepository $lr, AbonneRepository $ar)
    {
        $livres = $abonnes = $livres_indisponibles = null;

        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($rq);
        if( $form->isSubmitted() && $form->isValid() ){
            $mot = $form->get("mot")->getData();
            $livres = $lr->recherche($mot);
            $abonnes = $ar->recherche($mot);
            $livres_indisponibles = $lr->findLivresIndisponibles();
        }

        $formRecherche = $form->createView();
        return $this->render("recherche/globale.html.twig", compact("formRecherche", "livres", "abonnes", "livres_indisponibles"));
    }
}
