<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\Jann\Environment\Entity\ZombieState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ZombieState|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZombieState|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZombieState[]    findAll()
 * @method ZombieState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZombieStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZombieState::class);
    }

    public function findOrCreate(Zombie $zombie, int $count)
    {
        $matches = $this->findBy([
            'health' => $zombie->getHealth(),
            'zombieType' => $zombie->getZombieType()
        ]);

        if (count($matches) > 0) {
            return $matches[0];
        }

        $zombieState = new ZombieState(
            $zombie,
            $count
        );

        $this->_em->persist($zombieState);
        $this->_em->flush($zombieState);

        return $zombieState;
    }
}
