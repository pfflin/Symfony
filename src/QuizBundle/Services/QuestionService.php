<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/4/2019
 * Time: 11:07 PM
 */

namespace QuizBundle\Services;


use QuizBundle\Repository\QuestionRepository;

class QuestionService implements QuestionServiceInterface
{
    private $questionRepository;
    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository=$questionRepository;
    }

    public function getAllQuestions()
    {
        return $this->questionRepository->findAll();
    }
}