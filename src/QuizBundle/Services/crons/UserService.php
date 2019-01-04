<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/3/2019
 * Time: 10:32 PM
 */

namespace QuizBundle\Services\crons;


use QuizBundle\Repository\UserRepository;

class UserService implements UserServiceInterface
{
    private $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository=$userRepository;
    }

    public function getRating()
    {
        $users = $this->userRepository->getAll();
        return $users;
    }
}