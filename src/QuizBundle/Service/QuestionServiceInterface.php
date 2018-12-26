<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 12/19/2018
 * Time: 3:33 PM
 */

namespace QuizBundle\Service;


use Symfony\Component\HttpFoundation\Request;

interface QuestionServiceInterface
{
    public function createQuestion(Request $request);
}