<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 12/19/2018
 * Time: 11:56 AM
 */

namespace QuizBundle\Service;


use QuizBundle\Entity\User;
use QuizBundle\Form\UserType;
use QuizBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserService implements UserServiceInterface
{
    private $userRepository;
    private $container;
    public function __construct(UserRepository $userRepository,ContainerInterface $container)
    {
        $this->userRepository = $userRepository;
        $this->container = $container;
    }

    public function register($request)
    {
        $user = new User();
        $form= $this->container->get('form.factory')->create(UserType::class,$user,array());
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $password = $this->container->get('security.password_encoder')
                ->encodePassword($user,$user->getPassword());
            $user->setPassword($password);
            $this->userRepository->register($user);
            return true;
        }
        return false;
    }
}