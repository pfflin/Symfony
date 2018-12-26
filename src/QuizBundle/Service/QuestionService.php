<?php
/**
 * Created by PhpStorm.
 * User: Pavlin
 * Date: 12/19/2018
 * Time: 3:34 PM
 */

namespace QuizBundle\Service;


use QuizBundle\Entity\Question;
use QuizBundle\Form\QuestionType;
use QuizBundle\Repository\QuestionRepository;
use QuizBundle\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class QuestionService implements QuestionServiceInterface
{
    /**
     * @var QuestionRepository $questionRepository
     */
    private $questionRepository;
    private $container;
    public function __construct(QuestionRepository $questionRepository,ContainerInterface $container)
    {
        $this->questionRepository = $questionRepository;
        $this->container = $container;
    }
    public function createQuestion(Request $request)
    {
        $question = new Question();
        $form= $this->container->get('form.factory')->create(QuestionType::class,$question,array());
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $this->questionRepository->addQuestion($question);
            return true;
        }
        return false;
    }
}