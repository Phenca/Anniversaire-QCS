<?php

namespace App\Controller;

use App\Entity\Birthday;
use App\Form\BirthdayType;
use App\Repository\BirthdayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

# Faire les fixtures + le trie par date qui arrivent dans les 40 prochains jours
#[Route('/birthday')]
class BirthdayController extends AbstractController
{
    #[Route('/', name: 'app_birthday_index', methods: ['GET', 'POST'])]
    public function index(Request $request, BirthdayRepository $birthdayRepository): Response
    {
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $birthdays = $birthdayRepository->findBy(
                ['date' => ['gte' => new \DateTime()]], // condition
                ['date' => 'ASC'], // order
                40 // limit
            );
        } else {
            $birthdays = $birthdayRepository->findAll();
        }
    
        return $this->render('birthday/index.html.twig', [
            'form' => $form->createView(),
            'birthdays' => $birthdays,
        ]);
    }
    

    #[Route('/new', name: 'app_birthday_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $birthday = new Birthday();
        $form = $this->createForm(BirthdayType::class, $birthday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($birthday);
            $entityManager->flush();

            return $this->redirectToRoute('app_birthday_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('birthday/new.html.twig', [
            'birthday' => $birthday,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_birthday_show', methods: ['GET'])]
    public function show(Birthday $birthday): Response
    {
        return $this->render('birthday/show.html.twig', [
            'birthday' => $birthday,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_birthday_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Birthday $birthday, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BirthdayType::class, $birthday);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_birthday_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('birthday/edit.html.twig', [
            'birthday' => $birthday,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_birthday_delete', methods: ['POST'])]
    public function delete(Request $request, Birthday $birthday, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$birthday->getId(), $request->request->get('_token'))) {
            $entityManager->remove($birthday);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_birthday_index', [], Response::HTTP_SEE_OTHER);
    }
}
