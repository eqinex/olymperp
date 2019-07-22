<?php


namespace AppBundle\Repository;
use AppBundle\Traits\RepositoryPaginatorTrait;
use Doctrine\ORM\QueryBuilder;

/**
 * BookRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

class BookRepository extends \Doctrine\ORM\EntityRepository
{
    use RepositoryPaginatorTrait;

    /**
     * @param $filters
     * @param $orderBy
     * @param $order
     * @return QueryBuilder
     */
    public function getAllBooks($filters, $orderBy, $order) {
        $qb = $this->createQueryBuilder('b');

        $qb->select('b');

        $qb = $this->applyFilters($qb, $filters);

        if (!empty($orderBy)) {
            if ($orderBy == 'title') {
                $qb->orderBy('b.title', $order);
            } elseif ($orderBy == 'author') {
                $qb->orderBy('b.author', $order);
            } elseif ($orderBy == 'genre') {
                $qb->orderBy('b.genre', $order);
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param $filters
     * @return QueryBuilder
     */
    protected function applyFilters(QueryBuilder $qb, $filters)
    {
        if (!empty($filters['title'])) {
            $qb
                ->andWhere($qb->expr()->like('b.title', ':title'))
                ->setParameter('title', '%' . $filters['title'] . '%');
        }
        if (!empty($filters['author'])) {
            $qb
                ->andWhere($qb->expr()->like('b.author', ':author'))
                ->setParameter('author', '%' . $filters['author'] . '%');
        }
        if (!empty($filters['genre'])) {
            $qb
                ->andWhere($qb->expr()->like('b.genre', ':genre'))
                ->setParameter('genre', '%' . $filters['genre'] . '%');
        }

        return $qb;
    }
}