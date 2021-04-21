<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Int_;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\Table(name="post", indexes={@ORM\Index(columns={"title","description"}, flags={"fulltext"})})
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="integer")
     * 1 = closed
     * 0 = open
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=PostView::class, mappedBy="post", orphanRemoval=true)
     */
    private $postViews;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->postViews = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * Get all users that comment this post
     * @return Array|User[]
     */
    public function getAuthorComments() {   
        $comments = $this->comments;
        $authors = [];
        foreach($comments as $comment) {
            if(!in_array($comment->getAuthor(),$authors)) {
                $authors [] = $comment->getAuthor();
            }
        }
        return $authors;
    }

    /**
     * @return Collection|PostView[]
     */
    public function getPostViews(): Collection
    {
        return $this->postViews;
    }

    public function getSumPostViews(): int {
        return sizeof($this->postViews);
    }

    public function addPostView(PostView $postView): self
    {
        if (!$this->postViews->contains($postView)) {
            $this->postViews[] = $postView;
            $postView->setPost($this);
        }

        return $this;
    }

    public function removePostView(PostView $postView): self
    {
        if ($this->postViews->removeElement($postView)) {
            // set the owning side to null (unless already changed)
            if ($postView->getPost() === $this) {
                $postView->setPost(null);
            }
        }

        return $this;
    }

}
