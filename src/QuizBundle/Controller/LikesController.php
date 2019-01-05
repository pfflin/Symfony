<?php

namespace QuizBundle\Controller;

use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Entity\User;
use QuizBundle\Services\LikesServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends Controller
{
    private $likesService;
    public function __construct(LikesServiceInterface $likesService)
    {
        $this->likesService = $likesService;
    }

    /**
     * @Route("/likeQuestion{id}", name="likeQuestion",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addLikeToQuestionAjax($id)
    {
        if ($this->likesService->addLikeQuestion($id)){
            return new Response("Thank you for your vote !", 200, array('Content-Type' => 'text/html'));
        }
        return new Response("You can't vote for your own questions", 200, array('Content-Type' => 'text/html'));
    }
    /**
     * @Route("/unlikeQuestion{id}", name="unlikeQuestion",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeLikeFromQuestionAjax($id)
    {
        $this->likesService->removeLikeQuestion($id);
            return new Response("Thank you for your vote !", 200, array('Content-Type' => 'text/html'));
    }
    /**
     * @Route("/likeQuestionAndRedirect/{id}", name="likeQuestionAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addLikeToQuestion($id)
    {
        $this->likesService->addLikeQuestion($id);
            return $this->redirectToRoute("getOne",array('id'=>$id));
    }
    /**
     * @Route("/unlikeQuestionAndRedirect/{id}", name="unlikeQuestionAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeLikeFromQuestion($id)
    {
        $this->likesService->removeLikeQuestion($id);
        return $this->redirectToRoute("getOne",array('id'=>$id));
    }
    /**
     * @Route("/likeCommentAndRedirect/{id}", name="likeCommentAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addLikeToComment($id)
    {
        $questionId = $this->likesService->addLikeComment($id);
        if ($questionId == 0){
            return new Response("You can't vote for your own questions", 200, array('Content-Type' => 'text/html'));
        }
        return $this->redirectToRoute("getOne",array('id'=>$questionId));
    }
    /**
     * @Route("/unlikeCommentAndRedirect/{id}", name="unlikeCommentAndRedirect",requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeLikeFromComment($id)
    {
        $questionId = $this->likesService->removeLikeComment($id);
        if ($questionId == 0){
            return new Response("You can't remove a vote which you haven't committed", 200, array('Content-Type' => 'text/html'));
        }
        return $this->redirectToRoute("getOne",array('id'=>$questionId));
    }
}
