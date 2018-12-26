<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 12/19/2018
 * Time: 11:54 AM
 */

namespace QuizBundle\Service;


use QuizBundle\Entity\User;

interface UserServiceInterface
{
    public function register($request);
}