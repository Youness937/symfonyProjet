<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Produit;
use App\Form\FormationType;
use Symfony\Component\HttpFoundation\Request;

class EmpServFormationController extends AbstractController
{


    #[Route('/serv/afficher-formations', name: 'serv_afficher_formations')]
    public function afficherFormationsAction(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $formations = $em->getRepository(Formation::class)->findAll();

        return $this->render('emp_serv_formation/afficherFormations.html.twig', [
            'formations' => $formations,
            'controller_name' => 'EmpServFormationController',
        ]);
    }



    #[Route('/serv/ajoutformation', name: 'serv_formation_ajouter')]
    public function ajoutFormationAction(Request $request, ManagerRegistry $doctrine, Formation $formation = null)
    {
        if ($formation == null) {
            $formation = new Formation();
        }
        $form = $this->createForm(FormationType::class, $formation);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($formation);
            $em->flush();
            return $this->redirectToRoute('serv_afficher_formations');
        }

        return $this->render('emp_serv_formation/ajoutFormation.html.twig', [
            'form' => $form->createView(), // Ajout du rendu du formulaire dans le template
            'controller_name' => 'EmplServFormationController',
        ]);
    }



    #[Route('/serv/modifier-formation/{id}', name: 'serv_formation_modifier')]
    public function modifierFormationAction(Request $request, ManagerRegistry $doctrine, $id)
    {
        $em = $doctrine->getManager();
        $formation = $em->getRepository(Formation::class)->find($id);
        

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée pour l\'ID ' . $id);
        }

        $form = $this->createForm(FormationType::class, $formation);
        
        // Récupération de la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('serv_afficher_formations');
        }

        return $this->render('emp_serv_formation/modifierFormation.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'EmplServFormationController',
        ]);
    }


    #[Route('/serv/supprimer-formation/{id}', name: 'serv_formation_supprimer')]
    public function supprimerFormationAction(Request $request, ManagerRegistry $doctrine,$id): Response
    {
        $em = $doctrine->getManager();
        $formation = $em->getRepository(Formation::class)->find($id);
        
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée pour l\'ID ' . $id);
        }

        // Vérifier s'il y a des inscriptions associées à cette formation
        $inscriptions = $em->getRepository(Inscription::class)->findBy(['laformation' => $formation]);

        if (!empty($inscriptions)) {
            // Si des inscriptions sont associées, afficher un message d'erreur
            $this->addFlash('danger', 'Impossible de supprimer la formation car des inscriptions y sont associées.');
        } else {
            // Supprimer la formation
            $em->remove($formation);
            $em->flush();

            $this->addFlash('success', 'Formation supprimée avec succès.');
        }

        return $this->redirectToRoute('serv_afficher_formations');
    }

    #[Route('/serv_afficher_inscriptions', name: 'serv_afficher_inscriptions')]
    public function afficherInscriptionsAction(ManagerRegistry $doctrine,): Response
    {
        // Récupérer les inscriptions de l'utilisateur avec le statut 0
        $inscriptionRepository = $doctrine->getManager()->getRepository(Inscription::class);
        $inscriptions = $inscriptionRepository->findBy(['statut' => 0]);

        return $this->render('emp_serv_formation/afficherLesInscriptions.html.twig', [
            'inscriptions' => $inscriptions,
            'controller_name' => 'EmpServFormationController',
        ]);
    }

    #[Route('/serv/supprimer-inscription/{id}', name: 'serv_inscription_supprimer')]
    public function supprimerInscriptionAction(Request $request, ManagerRegistry $doctrine,$id): Response
    {
        $em = $doctrine->getManager();
        $inscription = $em->getRepository(Inscription::class)->find($id);
        
        if (!$inscription) {
            throw $this->createNotFoundException('Inscription non trouvée pour l\'ID ' . $id);
        }

        // Supprimer la formation
        $em->remove($inscription);
        $em->flush();

        $this->addFlash('danger', 'Inscription refusée avec succès.');
        

        return $this->redirectToRoute('serv_afficher_inscriptions');
    }

    #[Route('/serv/valider-inscription/{id}', name: 'serv_inscription_valider')]
    public function validerInscriptionAction(Request $request, ManagerRegistry $doctrine,$id): Response
    {
        $em = $doctrine->getManager();
        $inscription = $em->getRepository(Inscription::class)->find($id);

        if (!$inscription) {
            throw $this->createNotFoundException('Inscription non trouvée');
        }

        // Valider l'inscription en changeant le statut à 1
        $inscription->setStatut(1);
        $em->flush();

        $this->addFlash('success', 'Inscription validée avec succès.');

        // Rediriger vers la page où les inscriptions sont affichées
        return $this->redirectToRoute('serv_afficher_inscriptions');
    }
}
