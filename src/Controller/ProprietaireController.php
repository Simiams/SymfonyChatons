<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Proprietaire;
use App\Form\ProprietaireSupprimerType;
use App\Form\ProprietaireType;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Cast\Bool_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProprietaireController extends AbstractController
{
    /**
     * @Route("/proprietaire", name="app_proprietaire")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $proprietaire = new proprietaire();
        $form = $this->createForm(proprietaireType::class, $proprietaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($proprietaire);
            $em->flush();

        }
        $repo = $doctrine->getRepository(proprietaire::class);
        $proprietaires = $repo->findAll();

        return $this->render('proprietaire/index.html.twig', [
            'proprietaires' => $proprietaires,
            'formulaire' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/", name="")
//     */
    public function proprietaireList(ManagerRegistry $doctrine, Request $request): Response
    {
        $proprietaire = new proprietaire();
//        $form = $this->createForm(proprietaireType::class, $proprietaire);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $doctrine->getManager();
//            $em->persist($proprietaire);
//            $em->flush();
//        }
        $repo = $doctrine->getRepository(proprietaire::class);
        $proprietaires = $repo->findAll();

        return $this->render('proprietaire/proprietaireList.html.twig', [
            'proprietaires' => $proprietaires,
//            'formulaire' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/proprietaire/chatons/{id}", name="proprietaire_voir_chaton")
//     */
//    public function voirChatons(ManagerRegistry $doctrine, Request $request): Response
//    {
//        $proprietaire = $doctrine->getRepository(Categorie::class)->find($id);
//        //si on n'a rien trouv?? -> 404
//        if (!$proprietaire) {
//            throw $this->createNotFoundException("Aucun proprietaire avec l'id $proprietaire");
//        }
//
//        return $this->render('chatons/index.html.twig', [
//            'proprietaire' => $proprietaire,
//            "chatons" => $proprietaire->getChatons()
//        ]);
//    }


    /**
     * @Route("/proprietaire/modifier/{id}", name="proprietaire_modifier")
     */
    public function modifierProprietaire($id, ManagerRegistry $doctrine, Request $request)
    {
        //r??cup??rer la cat??gorie dans la BDD
        $proprietaire = $doctrine->getRepository(proprietaire::class)->find($id);

        //si on n'a rien trouv?? -> 404
        if (!$proprietaire) {
            throw $this->createNotFoundException("Aucune cat??gorie avec l'id $id");
        }

        //si on arrive l??, c'est qu'on a trouv?? une cat??gorie
        //on cr??e le formulaire avec (il sera rempli avec ses valeurs
        $form = $this->createForm(proprietaireType::class, $proprietaire);

        //Gestion du retour du formulaire
        //on ajoute Request dans les param??tres comme dans le projet pr??c??dent
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //le handleRequest a rempli notre objet $proprietaire
            //qui n'est plus vide
            //pour sauvegarder, on va r??cup??rer un entityManager de doctrine
            //qui comme son nom l'indique g??re les entit??s
            $em = $doctrine->getManager();
            //on lui dit de la ranger dans la BDD
            $em->persist($proprietaire);

            //g??n??rer l'insert
            $em->flush();

            //retour ?? l'accueil
            return $this->redirectToRoute("app_home");
        }

        return $this->render("proprietaire/modifier.html.twig", [
            'proprietaire' => $proprietaire,
            'formulaire' => $form->createView()
        ]);
    }

    /**
     * @Route("/proprietaire/supprimer/{id}", name="proprietaire_supprimer")
     */
    public function supprimerproprietaire($id, ManagerRegistry $doctrine, Request $request)
    {
        $proprietaire = $doctrine->getRepository(proprietaire::class)->find($id);
        if (!$proprietaire) {
            throw $this->createNotFoundException("Aucune cat??gorie avec l'id $id");
        }

        $form = $this->createForm(proprietaireSupprimerType::class, $proprietaire);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->remove($proprietaire);
            $em->flush();
            return $this->redirectToRoute("app_home");
        }

        return $this->render("proprietaire/supprimer.html.twig", [
            'proprietaire' => $proprietaire,
            'formulaire' => $form->createView()
        ]);
    }

}
