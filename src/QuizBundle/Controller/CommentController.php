<?php

namespace QuizBundle\Controller;

use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Form\CommentType;
use QuizBundle\Services\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    private $commentService;
    public function __construct(CommentServiceInterface $commentService)
    {
        $this->commentService=$commentService;
    }

    /**
     * @Route("/create/{id}",name="create_comment", requirements={"id"="\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */

    public function addComment(Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class,$comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->commentService->addComment($comment);
        }
        return $this->redirectToRoute('getOne', array('id'=>$comment->getQuestionId()));
    }

    /**
     * @Route("addCommentAjax",name="addCommentAjax")
     */
    public function addCommentAjax(){
        if (isset($_POST['comment'])){
            $comment = new Comment();
            $comment->setContent($_POST['comment']);
            $comment->setQuestionId($_POST['questionId']);
            $comment->setAuthorId($_POST['authorId']);
            $this->commentService->addComment($comment);
        }
        return new Response("Thank you for your comment", 200, array('Content-Type' => 'text/html'));
    }
    /**
     * @Route("/removeComment{id}", name="removeComment",requirements={"id"="\d+"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeComment($id)
    {
        $questionId = $this->commentService->removeComment($id);
        if ($questionId == 0){
            return $this->redirectToRoute("homepage");
        }
        return $this->redirectToRoute('getOne', array('id'=>$questionId));
    }
}
