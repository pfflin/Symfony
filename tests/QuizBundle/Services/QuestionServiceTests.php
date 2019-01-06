<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/5/2019
 * Time: 3:25 PM
 */

namespace Tests\QuizBundle\Services;


use QuizBundle\Entity\Question;
use QuizBundle\Services\QuestionService;
use QuizBundle\Services\QuestionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class QuestionServiceTests extends KernelTestCase
{
    private $container;
    public function __construct()
    {
        parent::__construct();
        $kernel = self::bootKernel();
        $this->container=$kernel->getContainer();
    }
    public function testCheckIfCorrectAnswerExistsIfItExists(){
        $question= new Question();
        $question->setCorrect("pesho");
        $question->setOpt1("gosho");
        $question->setOpt1("tisho");
        $question->setOpt1("pesho");
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->checkIfCorrectAnswerExists($question);
        $this->assertTrue($result);
    }
    public function testCheckIfCorrectAnswerExistsIfItDoesNot(){
        $question= new Question();
        $question->setCorrect("penko");
        $question->setOpt1("gosho");
        $question->setOpt1("tisho");
        $question->setOpt1("pesho");
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->checkIfCorrectAnswerExists($question);
        $this->assertFalse($result);
    }
    public function testCheckIfCorrectAnswerExistsIfItsEmpty(){
        $question= new Question();
        $question->setCorrect("");
        $question->setOpt1("gosho");
        $question->setOpt1("tisho");
        $question->setOpt1("pesho");
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->checkIfCorrectAnswerExists($question);
        $this->assertFalse($result);
    }
    public function testCheckIfUserIsOnTheRightPageWithRightPage(){
        $questionService = $this->container->get(QuestionService::class);
        $id = 5;
        $this->container->get('session')->set('page',$id);
        $result = $questionService->checkIfUserIsOnTheRightQuestion($id);
        $this->assertFalse($result);
    }
    public function testCheckIfUserIsOnTheRightPageWithWrongPage(){
        $questionService = $this->container->get(QuestionService::class);
        $id = 5;
        $this->container->get('session')->set('page',$id + 1);
        $result = $questionService->checkIfUserIsOnTheRightQuestion($id);
        $this->assertTrue($result);
    }
    public function testGetQuestionActionWithCorrect(){
        $question= new Question();
        $question->setCorrect("pesho");
        $_POST['answer'] = "pesho";
        $this->container->get('session')->set('page', 1);
        $this->container->get('session')->set('score', 1);
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->getQuestionAction($question);
        $this->assertTrue($result);
        $this->assertEquals($this->container->get('session')->get('score'),2);
        $this->assertEquals($this->container->get('session')->get('page'),2);
    }
    public function testGetQuestionActionWithIncorrect(){
        $question= new Question();
        $question->setCorrect("pesho");
        $_POST['answer'] = "tisho";
        $this->container->get('session')->set('page', 1);
        $this->container->get('session')->set('score', 1);
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->getQuestionAction($question);
        $this->assertFalse($result);
        $this->assertEquals($this->container->get('session')->get('score'),1);
        $this->assertEquals($this->container->get('session')->get('page'),2);
    }
    public function testIfUserIsOnTheRightPageIfHeIs(){
        $id = 2;
        $this->container->get('session')->set('page', $id);
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->checkIfUserIsOnTheRightQuestion($id);
        $this->assertFalse($result);
    }
    public function testIfUserIsOnTheRightPageIfHeIsNot(){
        $id = 2;
        $this->container->get('session')->set('page', $id);
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->checkIfUserIsOnTheRightQuestion($id+1);
        $this->assertTrue($result);
    }
    public function testIfItsTimeForResultTrue(){
        $id = 3;
        $this->container->get('session')->set('mode', $id);
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->checkIfItsTimeForResult($id+1);
        $this->assertTrue($result);
    }
    public function testIfItsTimeForResultFalse(){
        $id = 3;
        $this->container->get('session')->set('mode', $id);
        $questionService = $this->container->get(QuestionService::class);
        $result = $questionService->checkIfItsTimeForResult($id);
        $this->assertFalse($result);
    }
}