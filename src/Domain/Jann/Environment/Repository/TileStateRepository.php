<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\Jann\Environment\Entity\TileState;
use App\Domain\Jann\Environment\Entity\ZombieState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TileState|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileState|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileState[]    findAll()
 * @method TileState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileStateRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    )
    {
        parent::__construct($registry, TileState::class);
    }

    public function findOrCreate(ArrayCollection $zombies): TileState
    {
        // $queryBuilder = $this->createQueryBuilder("tileState")
        //     ->join("tileState.zombieStates", "zombieState")
        //     ->where("zombieState.id IN (:ZOMBIE_LINKS)")
        //     ->andWhere("COUNT(zombieState) = (:ZOMBIE_LINKS_COUNT)");

        $zombieStateClass = ZombieState::class;
        $tileStateClass = TileState::class;

        $dql = "SELECT 
            ts0,
            zs0
        FROM $tileStateClass ts0
        LEFT JOIN ts0.zombieStates zs0
        WHERE zs0.id IN (:ZOMBIE_LINKS)
        AND :ZOMBIE_LINKS_COUNT = (
            SELECT
                zs1
            FROM $zombieStateClass zs1
            JOIN zs0.tileStates ts1
            WHERE ts1 = ts0
        )";

        $query = $this->_em->createQuery($dql);

        $query->setParameters([
            ":ZOMBIE_LINKS" => $zombies,
            ":ZOMBIE_LINKS_COUNT" => $zombies->count(),
        ]);

        $matches = $query->getResult();

        if (count($matches) > 0) {
            return $matches[0];
        }

        $tileState = new TileState();

        $zombies->forAll(fn(int $i, ZombieState $zombieState) => $tileState->addZombieState($zombieState));

        $this->_em->persist($tileState);
        $this->_em->flush($tileState);

        return $tileState;
    }
}
