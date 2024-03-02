<?php

namespace App\Controller;

use App\Entity\Birthday;
use App\Form\BirthdayFilterType;
use App\Form\BirthdayType;
use App\Repository\BirthdayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/birthday')]
class BirthdayController extends AbstractController
{
    #[Route('/', name: 'app_birthday_index', methods: ['GET', 'POST'])]
    public function index(Request $request, BirthdayRepository $birthdayRepository): Response
    {
        $filter_form = $this->createForm(BirthdayFilterType::class);
        $filter_form->handleRequest($request);
        $today = new \DateTime();
        
        $query = $birthdayRepository->createQueryBuilder('b');
        $query->addSelect('b.id', 'b.firstname', 'b.lastname', 'b.birthdate', 'SUBSTRING(b.birthdate, 6, 5) as month_day')
            ->addSelect('(CASE WHEN SUBSTRING(b.birthdate, 6, 5) >= :today THEN 0 ELSE 1 END) as past')
            ->setParameter('today', $today->format('m-d'))
            ->orderBy('past', 'ASC')
            ->addOrderBy('month_day', 'ASC');
        
        $birthdays = $query->setMaxResults(40)
            ->getQuery()
            ->getResult();
        /*
        Problème avec l'envoi du formulaire pour une raison inconnue,
        Il est bien soumis par le bouton et valide mais cela ne change pas la manière dont les dates sont ordonnées
        */
        // if ($filter_form->isSubmitted() && $filter_form->isValid()) {
        //     $checkboxValue = $filter_form->get('filter')->getData();
        //     $query = $birthdayRepository->createQueryBuilder('b');
        //     $query->addSelect('b.id', 'b.firstname', 'b.lastname', 'b.birthdate', 'SUBSTRING(b.birthdate, 6, 5) as HIDDEN month_day')
        //         ->addSelect('(CASE WHEN SUBSTRING(b.birthdate, 6, 5) >= :today THEN 0 ELSE 1 END) as HIDDEN past')
        //         ->setParameter('today', $today->format('m-d'))
        //         ->orderBy('past', 'ASC')
        //         ->addOrderBy('month_day', 'ASC');

            
        //     $birthdays = $query->setMaxResults(40)
        //         ->getQuery()
        //         ->getResult();
        // } else {
        //     $birthdays = $birthdayRepository->createQueryBuilder('b')
        //         ->orderBy('b.id', 'ASC')    
        //         ->getQuery()
        //         ->getResult();
        // } 
    
        return $this->render('birthday/index.html.twig', [
            'filter_form' => $filter_form,
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
