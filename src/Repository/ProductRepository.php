<?php
//------------------------------------------
// Fichier: ProductRepository.php
// Rôle: Classe modèle du repository des produit
// Création: 2021-04-17
// Par: Kevin St-Pierre
//--------------------------------------------

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[]
     */
    public function findAllByFilter($filter)
    {
        $where = '';
        if (isset($filter['search']))
            $where = $this->setWhere('(p.name LIKE :search
            OR p.description LIKE :search)');

        if (isset($filter['idBrand']))
            $where = $this->setWhere('p.brand = :brand', $where);

        if (isset($filter['idCategory']))
            $where = $this->setWhere('p.category = :category', $where);

        if ($where != '') {
            $qb = $this->createQueryBuilder('p')
                ->where($where);

            if (isset($filter['search']))
                $qb->setParameter('search', '%' . $filter['search'] . '%');

            if (isset($filter['idBrand']))
                $qb->setParameter('brand', $filter['idBrand']);

            if (isset($filter['idCategory']))
                $qb->setParameter('category', $filter['idCategory']);

            return $qb->getQuery()->execute();
        } else
            return $this->findAll();
    }

    /**
     * @return Product[]
     */
    public function findToOrder()
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.stockQty < p.minThresholdQty');


        return $qb->getQuery()->execute();
    }

    /**
     * @return Product[]
     */
    public function findNotToOrder()
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.stockQty >= p.minThresholdQty');


        return $qb->getQuery()->execute();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    private function setWhere(string $condition, string $where = '')
    {
        if ($where != '')
            return "$where AND $condition";
        else
            return $condition;
    }
}
