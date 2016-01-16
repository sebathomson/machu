<?php
/**
 * Created by PhpStorm.
 * User: alhorro
 * Date: 08.12.2015
 * Time: 18:34
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="tour_date")
 */
class TourDate {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $availability;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="dates")
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="tourDate", fetch="EAGER")
     */
    protected $reservations;

    public function __toString(){
        return $this->getCategory()->getName() . ' (' . $this->getDate()->format('d.m.Y') . ')';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return TourDate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set availability
     *
     * @param integer $availability
     * @return TourDate
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * Get availability
     *
     * @return integer 
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     * @return TourDate
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add reservations
     *
     * @param \AppBundle\Entity\Reservation $reservations
     * @return TourDate
     */
    public function addReservation(\AppBundle\Entity\Reservation $reservations)
    {
        $this->reservations[] = $reservations;

        return $this;
    }

    /**
     * Remove reservations
     *
     * @param \AppBundle\Entity\Reservation $reservations
     */
    public function removeReservation(\AppBundle\Entity\Reservation $reservations)
    {
        $this->reservations->removeElement($reservations);
    }

    /**
     * Get reservations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }
}
