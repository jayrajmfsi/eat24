<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
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
     * @ORM\Column(name="commentText", type="text")
     */
    private $commentText;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var PlacedOrder
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\PlacedOrder", mappedBy="comment")
     *
     */
    private $placedOrderId;

    /**
     * @var bool
     *
     * @ORM\Column(name="isComplaint", type="boolean", nullable=true)
     */
    private $isComplaint;

    /**
     * @var bool
     *
     * @ORM\Column(name="isPraise", type="boolean", nullable=true)
     */
    private $isPraise;


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
     * Set commentText
     *
     * @param string $commentText
     *
     * @return Comment
     */
    public function setCommentText($commentText)
    {
        $this->commentText = $commentText;

        return $this;
    }

    /**
     * Get commentText
     *
     * @return string
     */
    public function getCommentText()
    {
        return $this->commentText;
    }

    /**
     * Set isComplaint
     *
     * @param boolean $isComplaint
     *
     * @return Comment
     */
    public function setIsComplaint($isComplaint)
    {
        $this->isComplaint = $isComplaint;

        return $this;
    }

    /**
     * Get isComplaint
     *
     * @return bool
     */
    public function getIsComplaint()
    {
        return $this->isComplaint;
    }

    /**
     * Set isPraise
     *
     * @param boolean $isPraise
     *
     * @return Comment
     */
    public function setIsPraise($isPraise)
    {
        $this->isPraise = $isPraise;

        return $this;
    }

    /**
     * Get isPraise
     *
     * @return bool
     */
    public function getIsPraise()
    {
        return $this->isPraise;
    }

    /**
     * Set userId
     *
     * @param \AppBundle\Entity\User $userId
     *
     * @return Comment
     */
    public function setUserId(\AppBundle\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set placedOrderId
     *
     * @param \AppBundle\Entity\PlacedOrder $placedOrderId
     *
     * @return Comment
     */
    public function setPlacedOrderId(\AppBundle\Entity\PlacedOrder $placedOrderId = null)
    {
        $this->placedOrderId = $placedOrderId;

        return $this;
    }

    /**
     * Get placedOrderId
     *
     * @return \AppBundle\Entity\PlacedOrder
     */
    public function getPlacedOrderId()
    {
        return $this->placedOrderId;
    }

}
