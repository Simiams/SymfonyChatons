<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Chaton;
use App\Entity\Proprietaire;
use App\Form\CategorieType;
use App\Form\ChatonType;
use App\Form\ChatonSupprimerType;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\never;

class ChatonsController extends AbstractController
{
    /**
     * @Route("/{source}/chatons/{id}", name="chaton_voir")
     */
    public function index($id, $source, ManagerRegistry $doctrine): Response
    {
        if ($source == "categorie") {
            $origin = $doctrine->getRepository(Categorie::class)->find($id);
            //si on n'a rien trouvé -> 404
            if (!$origin) {
                throw $this->createNotFoundException("Aucune catégorie avec l'id $id");
            }
        } else {
            $origin = $doctrine->getRepository(Proprietaire::class)->find($id);
            //si on n'a rien trouvé -> 404
            if (!$origin) {
                throw $this->createNotFoundException("Aucune catégorie avec l'id $id");
            }
        }

        return $this->render('chatons/index.html.twig', [
            'origin' => $origin,
            "chatons" => $origin->getChatons()
        ]);
    }

    /**
     * @Route("/chaton/ajouter/", name="chaton_ajouter")
     */
    public function ajouterChaton(ManagerRegistry $doctrine, Request $request)
    {
        $chaton = new Chaton();

        $form = $this->createForm(ChatonType::class, $chaton);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($chaton);
            $em->flush();

            //retour à l'accueil
            return $this->redirectToRoute("chaton_voir", ["id" => $chaton->getCategorie()->getId(), "source"=>"categorie"]);
        }

        return $this->render("chatons/ajouter.html.twig", [
            'formulaire' => $form->createView()
        ]);
    }

    /**
     * @Route("/chaton/supprimer/{id}", name="chaton_suprimer")
     */
    public function supprimerChaton($id, ManagerRegistry $doctrine, Request $request)
    {
        $chaton = $doctrine->getRepository(Chaton::class)->find($id);

        //si on n'a rien trouvé -> 404
        if (!$chaton) {
            throw $this->createNotFoundException("Aucun chaton avec l'id $id :(");
        }

        $form = $this->createForm(ChatonSupprimerType::class, $chaton);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->remove($chaton);

            $em->flush();

            return $this->redirectToRoute("chaton_voir", ["id" => $chaton->getCategorie()->getId(), "source" => "categorie"]);
        }

        return $this->render("chatons/supprimer.html.twig", [
            'chaton' => $chaton,
            'formulaire' => $form->createView()
        ]);
    }

    //     --- MODFIIER CHATONS ---

    /**
     * @Route("/chatons/modifier/{id}", name="chaton_modifier")
     */
    public function modifierChaton($id, ManagerRegistry $doctrine, Request $request)
    {
        $chaton = $doctrine->getRepository(Chaton::class)->find($id);

        if (!$chaton) {
            throw $this->createNotFoundException("Aucun chaton trouvé avec l'id $id");
        }

        $form = $this->createForm(ChatonType::class, $chaton);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            //le handleRequest a rempli notre objet $categorie
            //qui n'est plus vide
            //pour sauvegarder, on va récupérer un entityManager de doctrine
            //qui comme son nom l'indique gère les entités
            $em = $doctrine->getManager();
            //on lui dit de la ranger dans la BDD
            $em->persist($chaton);

            //générer l'insert
            $em->flush();

            //retour à l'accueil
            return $this->redirectToRoute("app_home");
        }


        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($chaton);

            $em->flush();

            return $this->redirectToRoute("chaton_voir", ["idCategorie" => $chaton->getCategorie()->getId()]);
        }

        return $this->render("chatons/modifier.html.twig", [
            'chaton' => $chaton,
            'formulaire' => $form->createView()
        ]);
    }
}
