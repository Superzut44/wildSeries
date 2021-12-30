<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season', name: 'season')]
class SeasonController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(SeasonRepository $seasonRepository): Response
    {
        return $this->render(
            'season/index.html.twig',
            [
            'seasons' => $seasonRepository->findAll(),
            ]
        );
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($season);
            $entityManager->flush();
            //var_dump($season->getProgram()->getTitle());
            //exit;
            $this->addFlash('success', "La nouvelle saison a été créée");

            return $this->redirectToRoute('season_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'season/new.html.twig',
            [
            'season' => $season,
            'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Season $season): Response
    {
        return $this->render(
            'season/show.html.twig',
            [
            'season' => $season,
            ]
        );
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('season_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'season/edit.html.twig',
            [
            'season' => $season,
            'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Season $season, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $entityManager->remove($season);
            $entityManager->flush();

            $this->addFlash('danger', "La saison a été supprimée");
        }

        return $this->redirectToRoute('season_index', [], Response::HTTP_SEE_OTHER);
    }
}
