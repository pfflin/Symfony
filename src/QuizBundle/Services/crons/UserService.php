<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/3/2019
 * Time: 10:32 PM
 */

namespace QuizBundle\Services\crons;


use QuizBundle\Entity\User;
use QuizBundle\Repository\RoleRepository;
use QuizBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;

class UserService implements UserServiceInterface
{
    private $userRepository;
    private $container;
    private $roleRepository;
    public function __construct(UserRepository $userRepository,ContainerInterface $container,RoleRepository $roleRepository)
    {
        $this->userRepository=$userRepository;
        $this->container=$container;
        $this->roleRepository=$roleRepository;
    }

    public function getRating()
    {
        $users = $this->userRepository->getAll();
        return $users;
    }

    public function registerUser(FormInterface $form,User $user)
    {
        $emailForm = $form->getData()->getEmail();
        $user1 = $this->userRepository->findOneBy(['email'=>$emailForm]);
        if (null !== $user1){
            return false;
        }
        $password = $this->container->get('security.password_encoder') ->encodePassword($user,$user->getPassword());
        $user->setPassword($password);
        /** @var Role $userRole */
        $userRole = $this->roleRepository->findOneBy(['name'=>'ROLE_USER']);
        $user->addRole($userRole);
        $this->userRepository->saveUser($user);
        return true;
    }
    public function ranking()
    {
          return $this->userRepository->getUsersOrderedByTotalRank();
    }
}