<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Produit;
use App\Entity\Employe;
use App\Repository\FormationRepository;
use App\Form\FormationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class EmpLambdaController extends AbstractController
{
    

    #[Route('/emp/afficher-formations', name: 'emp_afficher_formations')]
    public function afficherFormationsAction(ManagerRegistry $doctrine,): Response
    {
        $em = $doctrine->getManager();
        $formations = $em->getRepository(Formation::class)->findAll();

        return $this->render('emp_lambda/EmpFormations.html.twig', [
            'formations' => $formations,
            'controller_name' => 'EmpLambdaController',
        ]);
    }
    



    #[Route('/emp/s_inscrire_formation/{id}', name: 'emp_inscription_formation')]
    public function ajouterInscriptionAction(Request $request, ManagerRegistry $doctrine, SessionInterface $session, $id): Response
    {
        $em = $doctrine->getManager();
        $formation = $em->getRepository(Formation::class)->find($id);
        
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée pour l\'ID ' . $id);
        }

        // Récupérer l'utilisateur connecté
        $userId = $session->get('id');
        $userRepository = $doctrine->getManager()->getRepository(Employe::class);
        $user = $userRepository->findOneBy(['id' => $userId]);

        // Vérifier si l'utilisateur est déjà inscrit à cette formation
        $inscriptionRepository = $doctrine->getManager()->getRepository(Inscription::class);
        $existingInscription = $inscriptionRepository->findOneBy(['laformation' => $formation, 'lemploye' => $user]);

        if ($existingInscription) {
            $this->addFlash('danger', 'Vous êtes déjà inscrit à cette formation.');
            return $this->redirectToRoute('emp_afficher_formations');
        }

        // Création d'une nouvelle inscription
        $inscription = new Inscription();
        $inscription->setLaformation($formation);
        $inscription->setLemploye($user);
        $inscription->setStatut(0); // Statut 0 

        // Enregistrement de l'inscription
        $em->persist($inscription);
        $em->flush();

        $this->addFlash('success', 'Inscrit avec succès.');

        // Redirection vers une page de confirmation ou une autre action
        return $this->redirectToRoute('emp_afficher_formations');
    }

    #[Route('/emp/afficher-inscriptionsByUser', name: 'emp_afficher_les_formations')]
    public function afficherInscriptionsAction(ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        // Récupérer l'utilisateur connecté
        $userId = $session->get('id');
        $userRepository = $doctrine->getManager()->getRepository(Employe::class);
        $user = $userRepository->findOneBy(['id' => $userId]);

        // Récupérer les inscriptions de l'utilisateur
        $inscriptionRepository = $doctrine->getManager()->getRepository(Inscription::class);
        $inscriptions = $inscriptionRepository->findBy(['lemploye' => $user]);

        return $this->render('emp_lambda/EmpLesFormation.html.twig', [
            'inscriptions' => $inscriptions,
            'controller_name' => 'EmpLambdaController',
        ]);
    }


   


}
