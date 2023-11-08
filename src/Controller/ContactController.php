<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class ContactController extends AbstractController
{
    use TargetPathTrait;
    #[Route('/contact', name: 'app_contact')]
    public function index(SessionInterface $session, string $firewallName = 'main'): Response
    {
        $link = $this->generateUrl('app_contact');
        $this->saveTargetPath($session, $firewallName, $link);
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('page/contact.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
}
