<?php

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Question
 *
 * @ORM\Table(name="questions")
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\QuestionRepository")
 */
class Question
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="question", type="string", length=255, unique=true)
     */
    private $question;

    /**
     * @var string
     *
     * @ORM\Column(name="correct", type="string", length=255)
     */
    private $correct;

    /**
     * @var string
     *
     * @ORM\Column(name="opt1", type="string", length=255)
     */
    private $opt1;

    /**
     * @var string
     *
     * @ORM\Column(name="opt2", type="string", length=255)
     */
    private $opt2;

    /**
     * @var string
     *
     * @ORM\Column(name="opt3", type="string", length=255)
     */
    private $opt3;

    /**
     * @var int
     *
     * @ORM\Column(name="authorId", type="integer")
     */
    private $authorId;
    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Comment",mappedBy="question")
     */
    private $comments;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\User", inversedBy="questions")
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     */
    private $author;

    private $summary;

    /**
     * @var ArrayCollection
     * Many Questions have Many Users-likes.
     * @ManyToMany(targetEntity="User", mappedBy="likes")
     */
    private $users;

    function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->users = new  ArrayCollection();
    }
    public function isLiked(User $user){

        return $this->users->contains($user);
    }
    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
    /**
     * @param User $user
     * @return Question
     */
    public function setUsers (User $user)
    {
        $this->users[] = $user;
        return $this;

    }
    /**
     * @return mixed
     */
    public function getSummary()
    {
       if ($this->summary === null){
           $this->setSummary();
       }
       return $this->summary;
    }
    public function setSummary()
    {
        $this->summary = substr($this->question,0,22)."...";
    }
    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }
    /**
     * @param User $author
     * @return Question
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }
    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }
    /**
     * @param integer $authorId
     * @return Question
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }
    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }
    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set question
     *
     * @param string $question
     *
     * @return Question
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }
    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }
    /**
     * Set correct
     *
     * @param string $correct
     *
     * @return Question
     */
    public function setCorrect($correct)
    {
        $this->correct = $correct;

        return $this;
    }
    /**
     * Get correct
     *
     * @return string
     */
    public function getCorrect()
    {
        return $this->correct;
    }
    /**
     * Set opt1
     *
     * @param string $opt1
     *
     * @return Question
     */
    public function setOpt1($opt1)
    {
        $this->opt1 = $opt1;

        return $this;
    }
    /**
     * Get opt1
     *
     * @return string
     */
    public function getOpt1()
    {
        return $this->opt1;
    }
    /**
     * Set opt2
     *
     * @param string $opt2
     *
     * @return Question
     */
    public function setOpt2($opt2)
    {
        $this->opt2 = $opt2;

        return $this;
    }
    /**
     * Get opt2
     *
     * @return string
     */
    public function getOpt2()
    {
        return $this->opt2;
    }
    /**
     * Set opt3
     *
     * @param string $opt3
     *
     * @return Question
     */
    public function setOpt3($opt3)
    {
        $this->opt3 = $opt3;

        return $this;
    }
    /**
     * Get opt3
     *
     * @return string
     */
    public function getOpt3()
    {
        return $this->opt3;
    }
}

