<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderStatus
 *
 * @ORM\Table(name="order_status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderStatusRepository")
 */
class OrderStatus
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
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime")
     */
    private $ts;

    /**
     * @var PlacedOrder
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\PlacedOrder")
     * @ORM\JoinColumn(name="placed_order_id", referencedColumnName="id")
     */
    private $orderId;

    /**
     * @var StatusCatalog
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\StatusCatalog",)
     * @ORM\JoinColumn(name="status_catalog_id", referencedColumnName="id")
     */
    private $statusId;

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
     * Set ts
     *
     * @param \DateTime $ts
     *
     * @return OrderStatus
     */
    public function setTs($ts)
    {
        $this->ts = $ts;

        return $this;
    }

    /**
     * Get ts
     *
     * @return \DateTime
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * @return StatusCatalog
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * @param $statusId
     * @return OrderStatus
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;

        return $this;
    }
}

