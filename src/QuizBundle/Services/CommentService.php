<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/4/2019
 * Time: 12:06 PM
 */

namespace QuizBundle\Services;
use QuizBundle\Entity\Comment;
use QuizBundle\Entity\Question;
use QuizBundle\Repository\CommentRepository;
use QuizBundle\Repository\QuestionRepository;
use QuizBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Security;

class CommentService implements CommentServiceInterface
{
    private $userRepository;
    private $container;
    private $questionRepository;
    private $commentRepository;
    private $security;
    public function __construct(CommentRepository $commentRepository,QuestionRepository $questionRepository,UserRepository $userRepository,ContainerInterface $container,Security $security)
    {
        $this->userRepository=$userRepository;
        $this->container=$container;
        $this->questionRepository=$questionRepository;
        $this->commentRepository = $commentRepository;
        $this->security=$security;
    }
    public function addComment(Comment $comment)
    {
            $comment->setQuestion($this->questionRepository->find($comment->getQuestionId()));
            $comment->setAuthor($this->userRepository->find($comment->getAuthorId()));
            $this->commentRepository->saveComment($comment);
    }
    public function removeComment($id)
    {
        /** @var Comment $comment */
        $comment = $this->commentRepository->find($id);
        $currentUser = $this->security->getUser();
        if ($comment === null || !$currentUser->isAuthor($comment->getAuthorId()) && !$currentUser->isAdmin()){
            return 0;
        }
        $this->commentRepository->removeComment($comment);
        return $comment->getQuestionId();
    }
}