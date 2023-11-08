<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AboutController extends AbstractController
{
    use TargetPathTrait;
    #[Route('/about', name: 'app_about')]
    public function index(SessionInterface $session, string $firewallName = 'main'): Response
    {
        $link = $this->generateUrl('app_about');
        $this->saveTargetPath($session, $firewallName, $link);
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('page/about.html.twig', [
            'controller_name' => 'AboutController',
        ]);
    }
}
