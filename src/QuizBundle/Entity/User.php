<?php

namespace QuizBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="QuizBundle\Repository\UserRepository")
 */
class User implements UserInterface
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;

    /**
     * @var int
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Question", mappedBy="author", cascade={"remove"})
     */
    private $questions;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Comment",mappedBy="author", cascade={"remove"})
     */
    private $comments;

    /**
     * @var ArrayCollection
     * Many Users have liked many Questions.
     * @ORM\ManyToMany(targetEntity="QuizBundle\Entity\Question", inversedBy="users")
     * @ORM\JoinTable(name="users_questions",
     *      joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="questionId", referencedColumnName="id")}
     *      )
     */

    private $likes;

    /**
     * @var ArrayCollection
     * Many Users have liked many Comments.
     * @ORM\ManyToMany(targetEntity="QuizBundle\Entity\Comment", inversedBy="users")
     * @ORM\JoinTable(name="users_comments",
     *      joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="commentId", referencedColumnName="id")}
     *      )
     */

    private $likedComments;
    /**
     * @var ArrayCollection
     * @ManyToMany(targetEntity="QuizBundle\Entity\Role",inversedBy="users")
     * @JoinTable(name="users_roles",
     *     joinColumns={@JoinColumn(name="user_id",referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="role_id",referencedColumnName="id")})
     */
    private $roles;
    /**
     * @var int
     *
     * @ORM\Column(name="rank_from_quiz", type="integer", nullable=true, options={"default" : 0})
     */
    private $rankFromQuiz = 0;
    private $rankFromQuestions;
    private $rankFromLikes;
    /**
     * @var int
     *
     * @ORM\Column(name="total_rank", type="integer", nullable=true, options={"default" : 0})
     */
    private $totalRank =0;
    public function __construct()
    {
        $this->questions =new ArrayCollection();
        $this->comments =new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->likedComments = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTotalRank()
    {
        return $this->totalRank;
    }

    /**
     * @return int
     */
    public function getRankFromQuiz()
    {
        return $this->rankFromQuiz;
    }
    /**
     * @param int $rankFromQuiz
     */
    public function setRankFromQuiz($rankFromQuiz)
    {
        $this->rankFromQuiz = $rankFromQuiz;
    }
    private function setRankFromQuestions(){
        $this->rankFromQuestions = $this->questions->count();
    }
    private function setRankFromLikes(){
        $this->rankFromLikes=0;
        $closure = function($key, $element){
            $this->rankFromLikes += floor($element->getUsers()->count()/5);
            return true;
        };
        $this->questions->forAll($closure);
        $this->comments->forAll($closure);
    }

    /**
     * @return int
     */
    public function getRankFromQuestions()
    {
        return $this->rankFromQuestions;
    }

    /**
     * @return int
     */
    public function getRankFromLikes()
    {
        return $this->rankFromLikes;
    }
    public function setTotalRank($rank){
        $this->totalRank=$rank;
    }
    public function prepareTotalRank($rankFromQuiz)
    {
        $this->setRankFromQuestions();
        $this->setRankFromLikes();
        $this->totalRank = $this->rankFromLikes + $this->rankFromQuestions + $rankFromQuiz;
    }

    public function isCommented(Question $question){
        $closure = function($key, $element) use ($question){
            return $question->getId() === $element->getQuestionId();
        };
         if ($this->comments->exists($closure)){
             return true;
         };
         return false;
    }
    /**
     * @return ArrayCollection
     */
    public function getLikedComments()
    {
        return $this->likedComments;
    }

    /**
     * @param Comment $comment
     * @return User
     */
    public function setLikedComments(Comment $comment)
    {
        $this->likedComments[] = $comment;
        return $this;
    }

    public function removeLikedComment(Comment $comment){
        $this->likedComments->removeElement($comment);
    }

    /**
     * @return ArrayCollection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param Question $like
     * @return User
     */
    public function setLikes(Question $like)
    {
        $this->likes[] = $like;
        return $this;
    }
    public function removeLikes(Question $question){
        $this->getLikes()->removeElement($question);
    }
    public function isAuthor($id){
        return $id == $this->getId();
    }
    public function likedQuestion(Question $question){
        return $this->likes->contains($question);
    }
    public function isAdmin(){
        return in_array("ROLE_ADMIN",$this->getRoles());
    }
    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }
    public function getCommentedQuestions(){
        $questions = new ArrayCollection();
        foreach ($this->comments as $comment){
            $questions[] = $comment->getQuestion();
        }
        return $questions;
    }

    /**
     * @param Comment $comment
     * @return User
     */
    public function setComments(Comment $comment)
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }
    /**
     * @param ArrayCollection $question
     * @return User
     */
    public function setQuestions($question)
    {
        $this->questions[] = $question;
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
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->roles as $role){
            /** @var $role Role */
            $stringRoles[] = $role->getRole();
        }
        return $stringRoles;
    }

    public function addRole(Role $role){
        $this->roles[]=$role;
        return $this;
    }
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }
}

