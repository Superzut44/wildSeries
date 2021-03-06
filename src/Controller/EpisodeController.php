<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Service\Slugify;
use App\Form\EpisodeType;
use Symfony\Component\Mime\Email;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/episode', name: 'episode')]
class EpisodeController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render(
            'episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
            ]
        );
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Slugify $slugify, MailerInterface $mailer): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Slug
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $entityManager->persist($episode);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouvel épisode a été créé');

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Un nouvelle épisode vient d\'être publiée !')
                ->html($this->renderView('episode/newEpisodeEmail.html.twig', ['episode' => $episode]));
            $mailer->send($email);

            return $this->redirectToRoute('episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
            ]
        );
    }

    #[Route('/{slug}', name: '_show', methods: ['GET'])]
    public function show(Episode $episode): Response
    {
        return $this->render(
            'episode/show.html.twig', [
            'episode' => $episode,
            ]
        );
    }

    #[Route('/{slug}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episode $episode, EntityManagerInterface $entityManager, Slugify $slugify): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Slug
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);

            $entityManager->flush();

            return $this->redirectToRoute('episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
            ]
        );
    }

    #[Route('/{slug}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Episode $episode, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager->remove($episode);
            $entityManager->flush();

            $this->addFlash('danger', "L'épisode a été supprimé");
        }

        return $this->redirectToRoute('episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
