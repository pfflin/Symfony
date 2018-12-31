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
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Question", mappedBy="author")
     */
    private $questions;
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuizBundle\Entity\Comment",mappedBy="author")
     */
    private $comments;

    /**
     * @var ArrayCollection
     * Many Users have liked many Questions.
     * @ORM\ManyToMany(targetEntity="QuizBundle\Entity\Question")
     * @ORM\JoinTable(name="users_questions",
     *      joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="questionId", referencedColumnName="id")}
     *      )
     */

    private $likes;

    /**
     * @var ArrayCollection
     * Many Users have liked many Comments.
     * @ORM\ManyToMany(targetEntity="QuizBundle\Entity\Comment")
     * @ORM\JoinTable(name="users_comments",
     *      joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="commentId", referencedColumnName="id")}
     *      )
     */

    private $likedComments;

    public function __construct()
    {
        $this->questions =new ArrayCollection();
        $this->comments =new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->likedComments = new ArrayCollection();

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
    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comment
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
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ["ROLE_USER"];
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

