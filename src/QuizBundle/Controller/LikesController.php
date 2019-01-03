<?php

namespace QuizBundle\Controller;

use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends Controller
{
    /**
     * @Route("/likeQuestion{id}", name="likeQuestion",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addLikeToQuestionAjax($id)
    {
        /**
         * @var Question $question
         */
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$question->getAuthorId() == $user->getId()){
            return new Response("You can't vote for your own questions", 200, array('Content-Type' => 'text/html'));
        }
        $user->setLikes($question);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        try{
            $em->flush();
            return new Response("Thank you for your vote !", 200, array('Content-Type' => 'text/html'));
        }catch (\Exception $exception){

            return new Response("You have already voted for this question.", 200, array('Content-Type' => 'text/html'));
        }
    }
    /**
     * @Route("/unlikeQuestion{id}", name="unlikeQuestion",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeLikeFromQuestionAjax($id)
    {
        /**
         * @var Question $question
         */
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $user->removeLikes($question);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        try{
            $em->flush();
            return new Response("Thank you for your vote !", 200, array('Content-Type' => 'text/html'));
        }catch (\Exception $exception){
            return new Response("You have already voted for this question.", 200, array('Content-Type' => 'text/html'));
        }
    }
    /**
     * @Route("/likeQuestionAndRedirect/{id}", name="likeQuestionAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addLikeToQuestion($id)
    {
        /**
         * @var Question $question
         */
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if ($question->getAuthorId() == $user->getId()){
            return new Response("You can't vote for your own questions", 200, array('Content-Type' => 'text/html'));
        }
        $user->setLikes($question);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("getOne",array('id'=>$id));
    }
    /**
     * @Route("/unlikeQuestionAndRedirect/{id}", name="unlikeQuestionAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeLikeFromQuestion($id)
    {
        /**
         * @var Question $question
         */
        $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $user->removeLikes($question);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
            $em->flush();
        return $this->redirectToRoute("getOne",array('id'=>$id));
    }
    /**
     * @Route("/likeCommentAndRedirect/{id}", name="likeCommentAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addLikeToComment($id)
    {
        /**
         * @var Comment $comment
         */
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if (!$comment->getAuthorId() == $user->getId()){
            return new Response("You can't vote for your own questions", 200, array('Content-Type' => 'text/html'));
        }
        $user->setLikedComments($comment);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("getOne",array('id'=>$comment->getQuestionId()));
    }
    /**
     * @Route("/unlikeCommentAndRedirect/{id}", name="unlikeCommentAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeLikeFromComment($id)
    {
        /**
         * @var Comment $comment
         */
        $comment = $this->getDoctrine()->getRepository(Comment::class)->find($id);
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $user->removeLikedComment($comment);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute("getOne",array('id'=>$comment->getQuestionId()));
    }
}
