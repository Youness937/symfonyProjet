<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\LoginType;
use App\Entity\Employe;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(Request $request, ManagerRegistry $doctrine, SessionInterface $session): Response
    {
        $form = $this->createForm(LoginType::class);

        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $userRepository = $doctrine->getManager()->getRepository(Employe::class);

            $mdp = $data->getMdp();
            $login = $data->getLogin();
            $mdpHash = md5($mdp.'15');
            $user = $userRepository->findOneBy(['login' => $login, 'mdp' => $mdpHash]);

            // Vérifier si l'utilisateur est trouvé dans la base de données
            if ($user) {
                $session->set('id', $user->getId());
                $session->set('statut', $user->getStatut());

                // Rediriger en fonction du statut de l'utilisateur
                if ($session->get('statut') == 1) {
                    return $this->redirectToRoute('serv_afficher_formations');
                    
                } else {
                    return $this->redirectToRoute('emp_afficher_formations');
                }
            }
        }

        // Si le formulaire n'est pas encore soumis, afficher simplement le formulaire
        return $this->render('login/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}



