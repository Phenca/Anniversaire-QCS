<?php

namespace App\Controller;

use App\Form\BirthdayFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BirthdayController extends AbstractController
{
    #[Route('/birthday', name: 'app_birthday')]
    public function index(): Response
    {
        $form = $this->createForm(BirthdayFormType::class);
        return $this->render('birthday/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
