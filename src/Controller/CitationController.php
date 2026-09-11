<?php

namespace App\Controller;

use App\Entity\Citation;
use App\Form\CitationType;
use App\Repository\CitationRepository;
use App\Service\CitationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CitationController extends AbstractController
{
    #[Route('/', name: 'app_citation_index', methods: ['GET'])]
    public function index(CitationRepository $citationRepository): Response
    {
        return $this->render('citation/index.html.twig', [
            'citations' => $citationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_citation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CitationService $citationService): Response
    {
        $citation = new Citation();

        $form = $this->createForm(
            CitationType::class, $citation
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $citationService->save($citation);
            $this->addFlash(
                'success',
                'Le produit a été ajouté avec succès.'
            );

            return $this->redirectToRoute(
                'app_citation_index');
        }

        return $this->render('citation/new-citation.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_citation_show', methods: ['GET'])]
    public function show(Citation $citation): Response
    {
        return $this->render('citation/show.html.twig', [
            'citation' => $citation,
        ]);
    }

    #[Route('/{id}', name: 'app_citation_delete', methods: ['POST'])]
    public function delete(Request $request, Citation $citation, CitationService $citationService): Response
    {
        if ($this->isCsrfTokenValid('delete' . $citation->getId(), $request->getPayload()->getString('_token'))) {
            $citationService->remove($citation);
            $this->addFlash('success', 'La citation a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_citation_index');
    }

    #[Route('/{id}/edit', name: 'app_citation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Citation $citation, CitationService $citationService): Response
    {
        $form = $this->createForm(CitationType::class, $citation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $citationService->save($citation);

            $this->addFlash('success', 'La citation a été modifiée avec succès.');

            return $this->redirectToRoute('app_citation_index');
        }

        return $this->render('citation/edit.html.twig', [
            'citation' => $citation,
            'form' => $form,
        ]);
    }
}
