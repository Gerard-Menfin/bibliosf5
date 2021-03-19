<?php

namespace App\Controller;

use App\Entity\Emprunt;
use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/profil")
 * 
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/", name="profil")
     */
    public function index(): Response
    {
        /* Pour récupérer l'utilisateur connecté, on peut utilise la méhtode getUser
            $utilisateurConnecte = $this->getUser()
            Mais on peut aussi le récupérer directement dans un fichier TWIG
        */
        return $this->render('profil/index.html.twig');
    }

    /**
     * @Route("/emprunter/livre/{id}", name="profil_emprunter")
     */
    public function emprunter(EntityManagerInterface $em, Livre $livre)
    {
        $emprunt = new Emprunt;
        $emprunt->setAbonne( $this->getUser() );
        $emprunt->setLivre( $livre );
        $emprunt->setDateEmprunt( new \DateTime() );
        $em->persist( $emprunt );
        $em->flush();
        $this->addFlash("info", "Votre emrunt a été enregistré");
        return $this->redirectToRoute("profil");
    }

    
}
