<?php

namespace QuizBundle\Repository;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use QuizBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new Mapping\ClassMetadata(User::class));
    }

    public function getAllUsers()
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select(User::class)
            ->from('User', 'u')
            ->orderBy('u.name', 'ASC');
        return $qb->getQuery()
            ->getResult();
    }
    public function getAll(){
       return $this->findAll();
    }
    public function saveUser(User $user){
       $this->_em->persist($user);
        $this->_em->flush();
    }
    public function getUsersOrderedByTotalRank(){
       return $this->findBy(array(),array('totalRank' => 'DESC'));
    }
    public function persistUser(User $user){
        $this->_em->persist($user);
    }
    public function flushAll(){
        $this->_em->flush();
    }
}
