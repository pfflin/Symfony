<?php

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var integer
     *
     * @ORM\Column(name="question_id", type="integer")
     */
    private $questionId;

    /**
     * @var int
     * @ORM\Column(name="authorId", type="integer")
     */
    private $authorId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\User",inversedBy="comments")
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     */
    private $author;


    /**
     * @var ArrayCollection
     * Many Comments have Many Users-likes.
     * @ManyToMany(targetEntity="User", mappedBy="likedComments")
     */
    private $users;
    function __construct()
    {
        $this->dateAdded=new \DateTime('now');
        $this->users = new ArrayCollection();
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
     * @return Comment
     */
    public function setUsers (User $user)
    {
        $this->users[] = $user;
        return $this;

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
     * @return Comment
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
     * @param int $authorId
     * @return Comment
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param Question $question
     * @return Comment
     */
    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }
    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="QuizBundle\Entity\Question", inversedBy="comments")
     * @ORM\JoinColumn(name="question_id",referencedColumnName="id")
     */
    private $question;

    /**
     * @return int
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @param int $questionId
     * @return Comment
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
        return $this;
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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     *
     * @return Comment
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
}

