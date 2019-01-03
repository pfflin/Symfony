<?php

namespace QuizBundle\Controller;

use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    /**
     * @Route("/addComment", name="create_comment")
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
            $question = $this->getDoctrine()->getRepository(Question::class)->find($comment->getQuestionId());
            $comment->setQuestion($question);
            $comment->setAuthor($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }
        return $this->redirectToRoute('getOne', array('id'=>$comment->getQuestionId()));
    }

    /**
     * @Route("/removeComment{id}", name="removeComment",requirements={"id"="\d+"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeComment($id)
    {/** @var Comment $comment */
            $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
                 $currentUser = $this->getUser();
        if ($comment === null || !$currentUser->isAuthor($comment->getAuthorId()) && !$currentUser->isAdmin()){
            return $this->redirectToRoute("homepage");
        }
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        return $this->redirectToRoute('getOne', array('id'=>$id));
    }
}
