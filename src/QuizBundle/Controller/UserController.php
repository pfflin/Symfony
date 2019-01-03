<?php

namespace QuizBundle\Controller;

use QuizBundle\Entity\Role;
use QuizBundle\Entity\User;
use QuizBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
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
            $emailForm = $form->getData()->getEmail();
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['email'=>$emailForm]);
            if (null !== $user){
                $this->addFlash('info', 'User with this email already exists');
                return $this->render('user/register.html.twig');
            }
            $password = $this->get('security.password_encoder')
                ->encodePassword($user,$user->getPassword());
            $user->setPassword($password);
            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            /** @var Role $userRole */
            $userRole = $roleRepository->findOneBy(['name'=>'ROLE_USER']);
            $user->addRole($userRole);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("security_login");
        }
        return $this->render('user/register.html.twig');
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile(){
        return $this->render("user/profile.html.twig",['user'=>$this->getUser()]);
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
        $users = $this->getDoctrine()->getRepository(User::class)->findBy(array(),array('totalRank' => 'DESC'));
        return $this->render("user/allUsers.html.twig",['users'=>$users]);
    }
}
