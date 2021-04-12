<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     */
    private $post;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $update_date;

    /**
     * @ORM\OneToMany(targetEntity=CommentRate::class, mappedBy="comment", orphanRemoval=true)
     */
    private $commentRates;

    /**
     * @ORM\ManyToOne(targetEntity=comment::class)
     */
    private $reference;

    public function __construct()
    {
        $this->commentRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(?\DateTimeInterface $update_date): self
    {
        $this->update_date = $update_date;

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
            $commentRate->setComment($this);
        }

        return $this;
    }

    public function removeCommentRate(CommentRate $commentRate): self
    {
        if ($this->commentRates->removeElement($commentRate)) {
            // set the owning side to null (unless already changed)
            if ($commentRate->getComment() === $this) {
                $commentRate->setComment(null);
            }
        }

        return $this;
    }

    /**
     * know if the logged user has rated up this comment
     *
     * @param User $user
     * @return boolean
     */
    public function isRatedUp(User $user) : bool {
        foreach($this->commentRates as $CommentRate){
            if($CommentRate->getUser() === $user && $CommentRate->getNote() === 1) return true;
        }

        return false;
    }

    /**
     * know if the logged user has rated down this comment
     *
     * @param User $user
     * @return boolean
     */
    public function isRatedDown(User $user) : bool {
        foreach($this->commentRates as $CommentRate){
            if($CommentRate->getUser() === $user && $CommentRate->getNote() === -1) return true;
        }

        return false;
    }

    public function sumCommentRates(): int {
        $sum =(int) 0;
        foreach($this->commentRates as $CommentRate){
            $sum += $CommentRate->getNote();
        }
        return $sum;    
    }

    public function getReference(): ?comment
    {
        return $this->reference;
    }

    public function setReference(?comment $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}
