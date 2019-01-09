<?php

namespace QuizBundle\Controller;

use Doctrine\DBAL\Connection;
use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Entity\User;
use QuizBundle\Form\CommentType;
use QuizBundle\Form\QuestionType;
use QuizBundle\Services\CommentServiceInterface;
use QuizBundle\Services\QuestionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends Controller
{

    private $commentService;
    private $questionService;
    public function __construct(CommentServiceInterface $commentService, QuestionServiceInterface $questionService)
    {
        $this->commentService=$commentService;
        $this->questionService=$questionService;
    }
    /**
     * @Route("/create",name="create_question")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createQuestion(Request $request)
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        if ($this->questionService->createQuestion($form,$question)){
            return $this->redirectToRoute("user_profile");
        }
        return $this->render('question/create.html.twig',['question'=>$question,'form'=>$form->createView()]);

    }

    /**
     * @param Connection $connection
     * @param  int $num
     * @Route("/start/{num}", name="start",requirements={"num"="^(5|10|15)$"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function startAction(Connection $connection,$num){

        $this->questionService->startGame($connection, $num);
        return $this->redirectToRoute("question",array('id'=>'1'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/question/{id}",name="question", requirements={"id"="\d+"})
     */
    public function getQuestionsAction($id, Request $request){
        if ($this->questionService->checkIfItsTimeForResult($id)){
            return $this->redirectToRoute("result");
        }
        if ($this->questionService->checkIfUserIsOnTheRightQuestion($id)){
            return $this->redirectToRoute("homepage");
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->commentService->addComment($comment);
        }
        $question = $this->questionService->getTheRightQuestionFromSession($id);
        if (isset($_POST['answer'])){
            if ($this->questionService->getQuestionAction($question)){
                return new Response("That's correct!", 200, array('Content-Type' => 'text/html'));
            }
            return new Response("Sorry, that was not the right answer", 200, array('Content-Type' => 'text/html'));
        }
        return $this->render("question/view.html.twig",['question'=>$question,'id'=>$id+1,'form'=>$form->createView()]);
    }
    /**
     * @return Response
     * @Route("/result", name="result")
     */
    public function getResult(){
        $page = $this->questionService->getPage();
        if ($this->questionService->checkIfItsTimeForResult($page)) {
            /** @var User $user */
            $user = $this->getUser();
            if ($this->questionService->checkIfAllAnswersAreCorrect()) {
               $this->questionService->addScoreToPlayer();
            return $this->render("question/result.html.twig");
        }
            $this->addFlash('info',"You didn't get all the answers right this time, try again");
            return $this->render("question/result.html.twig");
        }
        $this->addFlash('info',"You did not finish the whole quiz. Now you can start from the beginning");
        return $this->render("question/result.html.twig");
    }
    /**
     * @Route("/view/{id}", name="getOne", requirements={"id"="\d+"})
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function viewSingleQuestion($id,Request $request){
       $question = $this->questionService->getQuestion($id);
        if ($this->questionService->permitToViewQuestion($question)){
            return $this->redirectToRoute("homepage");
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->commentService->addComment($comment);
        }
        return $this->render('question/singleQuestion.html.twig',['question'=>$question]);
    }
    /**
     * @Route("/edit/{id}", name="edit",requirements={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editSingleQuestion(Request $request,$id){
        $question = $this->questionService->getQuestion($id);
        if ($this->questionService->permitToEditQuestion($question)){
            return $this->redirectToRoute("homepage");
        }
        $form = $this->createForm(QuestionType::class,$question);
        $form->handleRequest($request);
        $this->questionService->editQuestion($form,$question);
        return $this->render('question/edit.html.twig',['question'=>$question,'form'=>$form->createView()]);
    }
    /**
     * @Route("/delete/{id}", name="delete" ,requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteQuestion($id){
       $question = $this->questionService->getQuestion($id);
       if ($this->questionService->permitToEditQuestion($question)){
           return $this->redirectToRoute("homepage");
       }
        $this->questionService->deleteQuestion($question);
        return $this->redirectToRoute("homepage");
    }
}
