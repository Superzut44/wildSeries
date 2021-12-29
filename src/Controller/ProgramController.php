<?php

// src/Controller/ProgramController.php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Service\Slugify;
use App\Form\CommentType;
use App\Form\ProgramType;
use Symfony\Component\Mime\Email;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/program", name="program")
 */
class ProgramController extends AbstractController
{
    /**
     * The controller for the category add form
     * Display the form or deal with it
     *
     * @Route("/new", name="_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, Slugify $slugify, MailerInterface $mailer): Response
    {
        // Create a new Program Object
        $program = new Program();
        // Create the associated Form
        $form = $this->createForm(ProgramType::class, $program);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            // Add user actuel
            $program->setOwner($this->getUser());
            // Slug
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            // Persist Program Object
            $entityManager->persist($program);
            // Flush the persisted object
            $entityManager->flush();

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('your_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));
            $mailer->send($email);

            // Finally redirect to categories list
            return $this->redirectToRoute('program_index');
        }

        // Render the form
        return $this->render(
            'program/new.html.twig',
            [
            'program' => $program,
            "form" => $form->createView()]
        );
    }

    /**
     * @Route("/", name="_index")
     * @return     Response A reponse instance
     */
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * Getting a program by slug
     *
     * @Route("/{slug}",          name="_show")
     * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     * @return                    Response
     */
    public function show(Program $program): Response
    {
        return $this->render(
            'program/show.html.twig',
            [
            'program' => $program,
            ]
        );
    }

    /**
     * @Route("/{slug}/season/{season}", name="_season_show")
     * @ParamConverter("program",        class="App\Entity\Program", options={"mapping": {"slug": "slug"}})
     */
    public function showSeason(Program $program, Season $season): Response
    {
        return $this->render(
            'program/season_show.html.twig',
            [
            'program' => $program,
            'season' => $season,
            ]
        );
    }


    #[Route('/{program_slug}/season/{season}/episode/{episode_slug}', name: '_episode_show', methods: ['GET', 'POST'])]
    #[ParamConverter('program', options: ['mapping' => ['program_slug' => 'slug']])]
    #[ParamConverter('episode', options: ['mapping' => ['episode_slug' => 'slug']])]
    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEpisode(($episode));
            $comment->setAuthor($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();
        }

        return $this->renderForm(
            'program/episode_show.html.twig',
            [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'comments' => $commentRepository->findByEpisode($episode, ['id' => 'asc']),
            'form' => $form,
            ]
        );
    }

    #[Route('/{slug}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Program $program,
        Season $season,
        EntityManagerInterface $entityManager,
        Slugify $slugify
    ): Response {
        // Check wether the logged in user is the owner of the program
        if (!($this->getUser() == $program->getOwner())) {
            // If not the owner, throws a 403 Access Denied exception
            throw new AccessDeniedException('Only the owner can edit the program!');
        }
        $form = $this->createForm(programType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Slug
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);

            $entityManager->flush();

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm(
            'program/edit.html.twig',
            [
            'season' => $season,
            'program' => $program,
            'form' => $form,
            ]
        );
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }
}
