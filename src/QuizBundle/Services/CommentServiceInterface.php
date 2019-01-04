<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/4/2019
 * Time: 12:06 PM
 */

namespace QuizBundle\Services;


use QuizBundle\Entity\Comment;

interface CommentServiceInterface
{
    public function addComment(Comment $comment);
    public function removeComment($id);
}