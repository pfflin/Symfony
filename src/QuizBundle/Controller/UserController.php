<?php

namespace QuizBundle\Controller;

use QuizBundle\Entity\User;
use QuizBundle\Form\UserType;
use QuizBundle\Services\crons\UserServiceInterface;
use QuizBundle\Services\QuestionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    private $userService;
    private $questionService;
    public function __construct(UserServiceInterface $userService, QuestionServiceInterface $questionService)
    {
        $this->userService=$userService;
        $this->questionService=$questionService;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/register", name="user_register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form= $this->createForm(UserType::class,$user);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
           if (!$this->userService->registerUser($form,$user)) {
               $this->addFlash('info', 'User with this email already exists');
               return $this->render('user/register.html.twig');
           }
            return $this->redirectToRoute("security_login");
        }
        return $this->render('user/register.html.twig');
    }

    /**
     * @Route("/profile", name="user_profile")
     *
     */
    public function profile(){
        return $this->render("user/profile.html.twig",['questions'=>$this->questionService->getUsersQuestions()]);
    }
    /**
     * @Route("/profile/liked", name="user_likes")
     */
    public function likedQuestions(){
        return $this->render("user/profileLiked.html.twig",['user'=>$this->getUser()]);
    }
    /**
     * @Route("/profile/commented", name="user_commented")
     */
    public function commentedQuestions(){
        return $this->render("user/profileCommented.html.twig",['user'=>$this->getUser()]);
    }
    /**
     * @Route("/all/users", name="allUsers")
     */
    public function rating(){
        return $this->render("user/allUsers.html.twig",['users'=>$this->userService->ranking()]);
    }
    public function checkTotalRank(){
        $this->userService->updateUsersRanks();
        return "Users ranking updated";
    }

    /**
     * @Route("/admin/all", name="adminAll")
     */
    public function getAllQuestions(){
       $questions = $this->questionService->getAllQuestions();
        return $this->render("user/allQuestions.html.twig",['questions'=>$questions]);
    }
}
