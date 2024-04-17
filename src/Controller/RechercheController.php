<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Formation;
use App\Entity\Produit;
use App\Form\FormationType;
use App\Entity\Employe;
use App\Entity\Inscription;
use App\Repository\InscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Form\FormRechNom;
use App\Form\FormRechLibelle;

class RechercheController extends AbstractController
{
    #[Route('/rechercheFindBy', name: 'app_recherche_findby')]
    public function rechercheFindByAction(ManagerRegistry $doctrine)
    {
        $employes = $doctrine->getRepository(Employe::class)->findBy(['nom'=>'durand','statut'=>'0']);
        //var_dump($employes);
        //exit;
        return $this->render('recherche/employe.html.twig',array('ensEmployes'=>$employes, 'nom'=>'durand', 'statut'=>'0'));
        
    }


    #[Route('/rechercheInscriptionByNom', name: 'app_recherche_inscription_by_nom')]
    public function rechercheFormAction(Request $request, ManagerRegistry $doctrine)
    {
        $form = $this->createForm(FormRechNom::class);

        // Traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nom = $data['nom'];
            $prenom = $data['prenom'];

            $inscriptions = $doctrine->getManager()->getRepository(Inscription::class)->rechInscriptionsEmploye($nom, $prenom);

            // Appel direct de la méthode rechercheInscriptionAction avec les données saisies
            return $this->render('recherche/employeInscription.html.twig',[
            'inscriptions' => $inscriptions,'nom'=> $nom,'prenom'=>$prenom]);
        }

        return $this->render('recherche/formulaire.html.twig', [
            'form' => $form->createView(),
        ]);
    }

  



    #[Route('/rechercheInscriptionByLibelle', name: 'app_recherche_inscription_by_libelle')]
    public function rechercheLibelleFormAction(Request $request, ManagerRegistry $doctrine)
    {
        $form = $this->createForm(FormRechLibelle::class);

        // Traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $libelle = $data['libelle'];

            $inscriptions = $doctrine->getManager()->getRepository(Inscription::class)->rechInscriptionsLibelle($libelle);
            
            // Appel direct de la méthode rechercheInscriptionAction avec les données saisies
            return $this->render('recherche/inscriptionByLibelle.html.twig',[
                'inscriptions' => $inscriptions,'libelle'=> $libelle]);
        }

        return $this->render('recherche/formulaire2.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}