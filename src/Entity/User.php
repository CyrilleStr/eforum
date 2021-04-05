<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(
 *  fields={"email"},
 *  message="Un compte avec cette adresse mail existe déjà."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)  
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\EqualTo(propertyPath="confirm_password",message="Votre mdp doit correspondre")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="confirm_password",message="Votre mdp doit correspondre")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="author")
     */
    private $post;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="author")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=CommentRate::class, mappedBy="user", orphanRemoval=true)
     */
    private $commentRates;

    /**
     * @ORM\Column(type="datetime")
     */
    private $accountCreationDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $activity;

    /**
     * @ORM\ManyToMany(targetEntity=Badge::class, inversedBy="users")
     */
    private $badge;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="usersFollowed")
     */
    private $usersFollower;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="usersFollower")
     * @ORM\JoinTable(name="follow",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="follow_user_id", referencedColumnName="id")}
     *      )
     */
    private $usersFollowed;

    /**
     * @ORM\OneToMany(targetEntity=Notif::class, mappedBy="user", orphanRemoval=true)
     */
    private $notifs;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->commentRates = new ArrayCollection();
        $this->badge = new ArrayCollection();
        $this->usersFollower = new ArrayCollection();
        $this->usersFollowed = new ArrayCollection();
        $this->notifs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommentRate[]
     */
    public function getCommentRates(): Collection
    {
        return $this->commentRates;
    }

    public function addCommentRate(CommentRate $commentRate): self
    {
        if (!$this->commentRates->contains($commentRate)) {
            $this->commentRates[] = $commentRate;
            $commentRate->setUser($this);
        }

        return $this;
    }

    public function removeCommentRate(CommentRate $commentRate): self
    {
        if ($this->commentRates->removeElement($commentRate)) {
            // set the owning side to null (unless already changed)
            if ($commentRate->getUser() === $this) {
                $commentRate->setUser(null);
            }
        }

        return $this;
    }

    public function getAccountCreationDate(): ?\DateTimeInterface
    {
        return $this->accountCreationDate;
    }

    public function setAccountCreationDate(\DateTimeInterface $accountCreationDate): self
    {
        $this->accountCreationDate = $accountCreationDate;

        return $this;
    }

    public function getActivity(): ?string
    {
        return $this->activity;
    }

    public function setActivity(?string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * @return Collection|Badge[]
     */
    public function getBadge(): Collection
    {
        return $this->badge;
    }

    public function addBadge(Badge $badge): self
    {
        if (!$this->badge->contains($badge)) {
            $this->badge[] = $badge;
        }

        return $this;
    }

    public function removeBadge(Badge $badge): self
    {
        $this->badge->removeElement($badge);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getUsersFollower(): Collection
    {
        return $this->usersFollower;
    }

    public function addUsersFollower(self $usersFollower): self
    {
        if (!$this->usersFollower->contains($usersFollower)) {
            $this->usersFollower[] = $usersFollower;
        }

        return $this;
    }

    public function removeUsersFollower(self $usersFollower): self
    {
        $this->usersFollower->removeElement($usersFollower);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getUsersFollowed(): Collection
    {
        return $this->usersFollowed;
    }

    public function addUsersFollowed(self $usersFollowed): self
    {
        if (!$this->usersFollowed->contains($usersFollowed)) {
            $this->usersFollowed[] = $usersFollowed;
            $usersFollowed->addUsersFollower($this);
        }

        return $this;
    }

    public function removeUsersFollowed(self $usersFollowed): self
    {
        if ($this->usersFollowed->removeElement($usersFollowed)) {
            $usersFollowed->removeUsersFollower($this);
        }

        return $this;
    }
    

    /**
     * know if a user is already followed or not
     *
     * @param User $user
     * @return boolean
     */
    public function isUserFollowed(User $user) : bool {
        foreach($this->usersFollowed as $userFollowed){
            if($userFollowed === $user) return true;
        }

        return false;
    }

    /**
     * @return Collection|Notif[]
     */
    public function getNotifs(): Collection
    {
        return $this->notifs;
    }

    public function addNotif(Notif $notif): self
    {
        if (!$this->notifs->contains($notif)) {
            $this->notifs[] = $notif;
            $notif->setUser($this);
        }

        return $this;
    }

    public function removeNotif(Notif $notif): self
    {
        if ($this->notifs->removeElement($notif)) {
            // set the owning side to null (unless already changed)
            if ($notif->getUser() === $this) {
                $notif->setUser(null);
            }
        }

        return $this;
    }


}
