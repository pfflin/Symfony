<?php

namespace QuizBundle\Controller;

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
            $password = $this->get('security.password_encoder')
                ->encodePassword($user,$user->getPassword());
            $user->setPassword($password);
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
       $userId =  $this->getUser()->getId();
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        return $this->render("user/profile.html.twig",['user'=>$this->getUser()]);
    }
}
