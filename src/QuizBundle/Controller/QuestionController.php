<?php

namespace QuizBundle\Controller;

use Doctrine\DBAL\Connection;
use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Entity\User;
use QuizBundle\Form\CommentType;
use QuizBundle\Form\QuestionType;
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
    public function createQuestion(Request $request){
        $question = new Question();
        $form = $this->createForm(QuestionType::class,$question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            if ($question->getCorrect() == $question->getOpt1() || $question->getCorrect() == $question->getOpt2() || $question->getCorrect() == $question->getOpt3()){
                $em = $this->getDoctrine()->getManager();
                $question->setAuthor($this->getUser());
                $em->persist($question);
                $em->flush();
                return $this->redirectToRoute("user_profile");
            }

        }
        return $this->render('question/create.html.twig',['question'=>$question,'form'=>$form->createView(),'authorId'=>$this->getUser()->getId()]);
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/question/{id}",name="question", requirements={"id"="\d+"})
     */
    public function getQuestionsAction($id){
        $mode =  $this->get('session')->get('mode');
        //Check if user want's to skip questions or return to previous question
        if ($this->get('session')->get('page') != $id ){
            return $this->redirect("/start");
        }
        if ($id > $mode){
            return $this->redirectToRoute("result");
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
               return new Response("You can do better than this", 200, array('Content-Type' => 'text/html'));
           }
        }
        return $this->render("question/view.html.twig",['question'=>$question,'id'=>$id+1]);
    }
    /**
     * @return Response
     * @Route("/result", name="result")
     */
    public function getResult(){
        $mode =  $this->get('session')->get('mode');
        if ($this->get('session')->get('page') > $mode){
            return $this->render("question/result.html.twig",['score'=>$this->get('session')->get('score'),'mode'=>$this->get('session')->get('mode')]);
        }
        return $this->render("question/result.html.twig",['score'=>'You did not finish the whole quiz. Now you can start from the beginning']);
    }
    /**
     * @Route("/view/{id}", name="getOne", requirements={"id"="\d+"})
     * @param $id
     * @return Response
     */
    public function viewSingleQuestion($id){
        /**
         * @var Question $question
         */
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
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
