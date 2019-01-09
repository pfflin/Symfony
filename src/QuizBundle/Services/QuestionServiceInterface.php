<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/4/2019
 * Time: 11:06 PM
 */

namespace QuizBundle\Services;


use Doctrine\DBAL\Connection;
use QuizBundle\Entity\Question;
use Symfony\Component\Form\Form;


interface QuestionServiceInterface
{
    public function getAllQuestions();
    public function createQuestion(Form $form,Question $question);
    public function startGame(Connection $connection, $num);
    public function getQuestionAction(Question $question);
    public function checkIfUserIsOnTheRightQuestion($id);
    public function checkIfItsTimeForResult($id);
    public function getTheRightQuestionFromSession($id);
    public function getPage();
    public function checkIfAllAnswersAreCorrect();
    public function addScoreToPlayer();
    public function permitToViewQuestion(Question $question);
    public function permitToEditQuestion(Question $question);
    public function getQuestion($id);
    public function checkIfCorrectAnswerExists(Question $question);
    public function editQuestion(Form$form,Question$question);
    public function deleteQuestion(Question $question);
    public function getUsersQuestions();
}