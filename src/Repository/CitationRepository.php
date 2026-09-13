<?php

namespace App\Repository;

use App\Entity\Citation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Citation>
 */
class CitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Citation::class);
    }

    /**
     * @return string[]
     */
    public function findDistinctCategories(): array
    {
        return $this->findDistinctValues('category');
    }

    /**
     * @return string[]
     */
    public function findDistinctUniverses(): array
    {
        return $this->findDistinctValues('universe');
    }

    /**
     * @return string[]
     */
    public function findDistinctTags(): array
    {
        $rows = $this->createQueryBuilder('c')
            ->select('c.tags AS tags')
            ->getQuery()
            ->getArrayResult();

        $tags = [];

        foreach ($rows as $row) {
            $value = $row['tags'];

            if (is_string($value)) {
                $value = json_decode($value, true) ?: [];
            }

            foreach ((array) $value as $tag) {
                $tag = trim((string) $tag);

                if ($tag !== '') {
                    $tags[$tag] = $tag;
                }
            }
        }

        $tags = array_values($tags);
        sort($tags);

        return $tags;
    }

    /**
     * @return string[]
     */
    private function findDistinctValues(string $field): array
    {
        $rows = $this->createQueryBuilder('c')
            ->select(sprintf('DISTINCT c.%s AS value', $field))
            ->andWhere(sprintf('c.%s IS NOT NULL', $field))
            ->andWhere(sprintf("c.%s != ''", $field))
            ->orderBy(sprintf('c.%s', $field), 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_column($rows, 'value');
    }

    //    /**
    //     * @return Citation[] Returns an array of Citation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Citation
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
