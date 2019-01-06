<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/4/2019
 * Time: 11:07 PM
 */

namespace QuizBundle\Services;


use Doctrine\DBAL\Connection;
use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Entity\User;
use QuizBundle\Repository\QuestionRepository;
use QuizBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class QuestionService implements QuestionServiceInterface
{
    private $questionRepository;
    private $container;
    private $security;
    private $session;
    private $userRepository;
    public function __construct(UserRepository $userRepository, QuestionRepository $questionRepository,ContainerInterface $container,Security $security,SessionInterface$session)
    {
        $this->questionRepository=$questionRepository;
        $this->container=$container;
        $this->security=$security;
        $this->session=$session;
        $this->userRepository=$userRepository;
    }

    public function getAllQuestions()
    {
        return $this->questionRepository->findAll();
    }
    public function createQuestion(Form $form, Question $question){
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->container->get('session')->get('addQuestion') == "true") {
                if ($this->checkIfCorrectAnswerExists($question)) {
                    $question->setAuthor($this->security->getUser());
                    $this->questionRepository->saveQuestion($question);
                    $this->container->get('session')->set("addQuestion", "false");
                    $this->session->getFlashBag()->add("info", "Thank you for your question");
                    return true;
                }
                $this->session->getFlashBag()->add("info", "The correct answer must be exactly the same as one of the answers");
                return false;
            }
            $this->session->getFlashBag()->add("info", "First you have to pass the test perfectly with at least 10 questions");
            return false;
        }
    }
    public function startGame(Connection $connection, $num)
    {
       $arr = $this->questionRepository->getRandom($connection,$num);
        $this->container->get('session')->set('arr', json_encode($arr));
        $this->container->get('session')->set('score', 0);
        $this->container->get('session')->set('page', 1);
        $this->container->get('session')->set('mode', intval($num));
    }
    public function getQuestionAction(Question $question)
    {
            $choosed = $_POST['answer'];
            $this->container->get('session')->set('page', $this->container->get('session')->get('page') + 1);
            if (trim($choosed) === $question->getCorrect()){
                $this->container->get('session')->set('score', $this->container->get('session')->get('score') + 1);
                return true;
            }
            else {
                return false;
            }
    }
    public function checkIfUserIsOnTheRightQuestion($id)
    {
        if ($this->container->get('session')->get('page') != $id ){
            return true;
        }
        return false;
    }
    public function checkIfItsTimeForResult($id)
    {
        $mode =  $this->container->get('session')->get('mode');
        if ($id > $mode){
         return true;
        }
        return false;
    }
    public function getTheRightQuestionFromSession($id)
    {
        $arr =  json_decode($this->container->get('session')->get('arr'));
        $questionId = $arr[$id-1];
       return $this->questionRepository->find($questionId);
    }
    public function getPage(){
        return $this->container->get('session')->get('page');
    }
    public function checkIfAllAnswersAreCorrect()
    {
       return $this->container->get('session')->get('score') === $this->container->get('session')->get('mode');
    }
    public function addScoreToPlayer()
    {
        $score = $this->container->get('session')->get('score')/5;
        if ($score === 3){
            $score +=3;
        }
        /** @var User $user */
        $user = $this->security->getUser();
        $user->setRankFromQuiz($user->getRankFromQuiz() + $score);
        $this->userRepository->saveUser($user);
        $this->container->get('session')->set('score',0);
            $this->session->getFlashBag()->add("info", "You Have been rewarded with $score points");
        if ($score > 1){
            $this->session->getFlashBag()->add('question',"You can contribute and add a Question");
            $this->container->get('session')->set("addQuestion", "true");
        }
    }
    public function permitToViewQuestion(Question $question)
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        // if such question does not exist, or if user hasn't liked or comment on it and is not author or admin redirect...
        if ($question === null || !$currentUser->likedQuestion($question) && !$currentUser->isCommented($question) && !$currentUser->isAdmin() && !$currentUser->isAuthor($question->getAuthorId())){
            return true;
        }
    }
    public function getQuestion($id)
    {
        return $this->questionRepository->find($id);
    }
    public function permitToEditQuestion(Question $question)
    {
        $currentUser = $this->security->getUser();
        if ($question === null || !$currentUser->isAuthor($question->getAuthorId()) && !$currentUser->isAdmin()){
            return true;
        }
    }
    public function checkIfCorrectAnswerExists(Question $question)
    {
        if ($question->getCorrect() === $question->getOpt1() || $question->getCorrect() === $question->getOpt2() || $question->getCorrect() === $question->getOpt3()){
            return true;
        }
        return false;
    }
    public function editQuestion(Form $form, Question $question)
    {
        if ($form->isSubmitted() && $form->isValid()){
            if ($this->checkIfCorrectAnswerExists($question)) {
                 $question->setAuthorId($this->security->getUser()->getId());
                $this->questionRepository->saveQuestion($question);
            }
            $this->session->getFlashBag()->add("info", "One of the answers must be exactly the same as the correct answer");
        }
    }
    public function deleteQuestion(Question $question)
    {
     $this->questionRepository->deleteQuestion($question);
    }
}