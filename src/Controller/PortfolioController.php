<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Form\PostFormType;
use App\Entity\Post;

class PortfolioController extends AbstractController
{
    use TargetPathTrait;
    #[Route('/portfolio', name: 'app_portfolio')]
    public function index(SessionInterface $session, string $firewallName = 'main'): Response
    {
        $link = $this->generateUrl('app_portfolio');
        $this->saveTargetPath($session, $firewallName, $link);
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('page/port.html.twig', [
            'controller_name' => 'PortfolioController',
        ]);
    }

    #[Route('/new', name: 'app_new')]
    public function newPost(ManagerRegistry $doctrine, Request $request, SessionInterface $session, SluggerInterface $slugger, string $firewallName = 'main'): Response
    {
        $link = $this->generateUrl('app_portfolio');
        $this->saveTargetPath($session, $firewallName, $link);
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $file = $form->get('file')->getData();
                if ($file) {
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                    try {
                        $file->move(
                            $this->getParameter('images_directory'), $newFilename
                        );
                        $filesystem = new Filesystem();
                        $filesystem->copy(
                            $this->getParameter('images_directory') . '/' . $newFilename, true
                        );
                    } catch (FileException $e) {
                        
                    }
                    $post->setFile($newFilename);
                }
                $post = $form->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->redirectToRoute('app_blog');
            }
        return $this->render('new.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
