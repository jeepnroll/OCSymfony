<?php

namespace OC\PlatformBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * @ORM\Entity
 * @ORM\Table(name="advert_repository")
 */
class AdvertRepository extends EntityRepository
{
    /*
     * But : Récupérer les toutes les annonces de une ou plusieurs catégorie(s)
     */
    /**
     * @name getAdvertWithCategories
     * @param array $categoryNames
     * @return array
     */
    public function  getAdvertWithCategories(array $categoryNames){
        $qb = $this->createQueryBuilder("a");

        // On fait une jointure avec l'entité catégory avec pour alias « c »
        $qb
            ->innerJoin("a.categories", "c")
            ->addSelect("c");

        // On filtre
        $qb->where($qb->expr()->in("c.name", $categoryNames));

        // On retourne le résultat
        return $qb
            ->getQuery()
            ->getResult();
    }

    public function getAdverts($page, $nbPerPage)
    {
        $query = $this->createQueryBuilder("a")
            // Jointure sur l'attribut image
            ->leftJoin("a.image", "i")
            ->addSelect("i")
            // Jointure sur les catégories
            ->leftJoin("a.categories", "c")
            ->addSelect("c")
            // On trie
            ->orderBy("a.date", "DESC")
            // On récupère la requête
            ->getQuery();

        $query
            // On définit l'annonce à partir de laquelle commencer la liste
            ->setFirstResult(($page - 1) * $nbPerPage)
            ->setMaxResults($nbPerPage);

        return new Paginator($query, true);
    }
}

