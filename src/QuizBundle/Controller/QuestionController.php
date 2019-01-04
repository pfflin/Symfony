<?php

namespace QuizBundle\Controller;

use Doctrine\DBAL\Connection;
use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Entity\Text;
use QuizBundle\Entity\User;
use QuizBundle\Form\CommentType;
use QuizBundle\Form\QuestionType;
use QuizBundle\Services\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends Controller
{
    /**
     * @Route("/create",name="create_question")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    private $commentService;
    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService=$commentService;
    }

    public function createQuestion(Request $request)
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->get('session')->get("addQuestion") == "true") {
            if ($question->getCorrect() == $question->getOpt1() || $question->getCorrect() == $question->getOpt2() || $question->getCorrect() == $question->getOpt3()) {
                $em = $this->getDoctrine()->getManager();
                $question->setAuthor($this->getUser());
                $em->persist($question);
                $em->flush();
                $this->get('session')->set("addQuestion", "false");
                $this->addFlash("info", "Thank you for your question");
                return $this->redirectToRoute("user_profile");
            }
            $this->addFlash("info", "The correct answer must be exactly the same as one of the answers");
        }
            $this->addFlash("info", "First you have to pass the test perfectly with at least 10 questions");
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

        $arr = $this->getDoctrine()->getRepository(Question::class)->getRandom($connection,$num);
        $this->get('session')->set('arr', json_encode($arr));
        $this->get('session')->set('score', 0);
        $this->get('session')->set('page', 1);
        $this->get('session')->set('mode', intval($num));

        return $this->redirectToRoute("question",array('id'=>'1'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/question/{id}",name="question", requirements={"id"="\d+"})
     */
    public function getQuestionsAction($id, Request $request){
        $mode =  $this->get('session')->get('mode');
        //Check if user want's to skip questions or return to previous question
        if ($this->get('session')->get('page') != $id ){
            return $this->redirectToRoute("homepage");
        }
        if ($id > $mode){
            return $this->redirectToRoute("result");
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->commentService->addComment($comment);
        }
        $arr =  json_decode($this->get('session')->get('arr'));
        $questionId = $arr[$id-1];
        $question = $this->getDoctrine()->getRepository(Question::class)->find($questionId);
        if (isset($_POST['answer'])){
           $choosed = $_POST['answer'];
            $this->get('session')->set('page', $this->get('session')->get('page') + 1);
           if (trim($choosed) === $question->getCorrect()){
               $this->get('session')->set('score', $this->get('session')->get('score') + 1);
               return new Response("That's correct!", 200, array('Content-Type' => 'text/html'));
           }
           else{
               return new Response("Sorry, that was not the right answer", 200, array('Content-Type' => 'text/html'));
           }
        }
        return $this->render("question/view.html.twig",['question'=>$question,'id'=>$id+1,'form'=>$form->createView()]);
    }
    /**
     * @return Response
     * @Route("/result", name="result")
     */
    public function getResult(){
        $mode =  $this->get('session')->get('mode');
        if ($this->get('session')->get('page') > $mode) {
            /** @var User $user */
            $user = $this->getUser();
            if ($this->get('session')->get('score') === $this->get('session')->get('mode')) {
                $score = $this->get('session')->get('score')/5;
                if ($score === 3){
                    $score +=3;
                }
                $user->setRankFromQuiz($user->getRankFromQuiz() + $score);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->get('session')->set('score',0);
                $this->addFlash('info',"You Have been rewarded with $score points");
                if ($score > 1){
                    $this->addFlash('info',"You can contribute and add a question");
                    $this->get('session')->set("addQuestion", "true");
                }
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
        /**
         * @var Question $question
         */
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        // if such question does not exist, or if user hasn't liked or comment on it and is not author or admin redirect...
        if ($question === null || !$currentUser->likedQuestion($question) && !$currentUser->isCommented($question) && !$currentUser->isAdmin() && !$currentUser->isAuthor($question->getAuthorId())){
            return $this->redirectToRoute("homepage");
        }
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->commentService->addComment($comment);
        }
        return $this->render('question/singleQuestion.html.twig',['question'=>$question, 'form'=> $form->createView()]);
    }
    /**
     * @Route("/edit/{id}", name="edit",requirements={"id"="\d+"})
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editSingleQuestion(Request $request,$id){
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        $currentUser = $this->getUser();
        if ($question === null || !$currentUser->isAuthor($question->getAuthorId()) && !$currentUser->isAdmin()){
            return $this->redirectToRoute("homepage");
        }
        $form = $this->createForm(QuestionType::class,$question);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            if ($question->getCorrect() == $question->getOpt1() || $question->getCorrect() == $question->getOpt2() || $question->getCorrect() == $question->getOpt3()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($question);
                $em->flush();
            }
        }
        return $this->render('question/edit.html.twig',['question'=>$question,'form'=>$form->createView()]);
    }
}
