<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 1/4/2019
 * Time: 9:33 PM
 */

namespace QuizBundle\Services;


interface LikesServiceInterface
{
    public function addLikeQuestion($id);
    public function removeLikeQuestion($id);
    public function addLikeComment($id);
    public function removeLikeComment($id);
}