<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/3/2019
 * Time: 10:32 PM
 */

namespace QuizBundle\Services\crons;


use QuizBundle\Entity\User;
use Symfony\Component\Form\FormInterface;

interface UserServiceInterface
{
    public function getRating();
    public function registerUser(FormInterface $form,User $user);
    public function ranking();
    public function updateUsersRanks();
    public function getCurrentUser();
}