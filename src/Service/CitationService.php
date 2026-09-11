<?php

namespace App\Service;

use App\Entity\Citation;
use App\Repository\CitationRepository;
use Doctrine\ORM\EntityManagerInterface;

class CitationService
{
    private CitationRepository $citationRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CitationRepository $citationRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->citationRepository = $citationRepository;
        $this->entityManager = $entityManager;
    }
    
    public function add(Citation $citation): void
    {
        $this->entityManager->persist($citation);
        $this->entityManager->flush();
    }
}