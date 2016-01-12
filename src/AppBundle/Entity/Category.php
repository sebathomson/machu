<?php
/**
 * Created by PhpStorm.
 * User: alhorro
 * Date: 08.12.2015
 * Time: 18:26
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 * @UniqueEntity({"name", "destination"})
 */
class Category {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $minAvailability = 50;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $customAvailability;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $customDate;

    /**
     * @ORM\ManyToOne(targetEntity="Destination")
     */
    protected $destination;

    /**
     * @ORM\ManyToOne(targetEntity="TourDate")
     */
    protected $date;

    /**
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="category", fetch="EAGER")
     */
    protected $reservations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString(){
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set destination
     *
     * @param \AppBundle\Entity\Destination $destination
     * @return Category
     */
    public function setDestination(\AppBundle\Entity\Destination $destination = null)
    {
        $this->destination = $destination;
    
        return $this;
    }

    /**
     * Get destination
     *
     * @return \AppBundle\Entity\Destination 
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Set date
     *
     * @param \AppBundle\Entity\TourDate $date
     * @return Category
     */
    public function setDate(\AppBundle\Entity\TourDate $date = null)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \AppBundle\Entity\TourDate 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add reservations
     *
     * @param \AppBundle\Entity\Reservation $reservations
     * @return Category
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

    /**
     * Set minAvailability
     *
     * @param integer $minAvailability
     * @return Category
     */
    public function setMinAvailability($minAvailability)
    {
        $this->minAvailability = $minAvailability;

        return $this;
    }

    /**
     * Get minAvailability
     *
     * @return integer 
     */
    public function getMinAvailability()
    {
        return $this->minAvailability;
    }

    /**
     * Set customAvailability
     *
     * @param integer $customAvailability
     * @return Category
     */
    public function setCustomAvailability($customAvailability)
    {
        $this->customAvailability = $customAvailability;

        return $this;
    }

    /**
     * Get customAvailability
     *
     * @return integer 
     */
    public function getCustomAvailability()
    {
        return $this->customAvailability;
    }

    /**
     * Set customDate
     *
     * @param \DateTime $customDate
     * @return Category
     */
    public function setCustomDate($customDate)
    {
        $this->customDate = $customDate;

        return $this;
    }

    /**
     * Get customDate
     *
     * @return \DateTime 
     */
    public function getCustomDate()
    {
        return $this->customDate;
    }
}