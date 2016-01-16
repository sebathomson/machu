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
    protected $baseAvailability = 200;

    /**
     * @ORM\Column(type="integer", length=4, nullable=true)
     */
    protected $year;

    /**
     * @ORM\Column(type="integer")
     */
    protected $minAvailability = 50;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $customDate;

    /**
     * @ORM\ManyToOne(targetEntity="Destination")
     */
    protected $destination;

    /**
     * @ORM\OneToMany(targetEntity="TourDate", mappedBy="category")
     */
    protected $dates;

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
     * Set baseAvailability
     *
     * @param integer $baseAvailability
     * @return Category
     */
    public function setBaseAvailability($baseAvailability)
    {
        $this->baseAvailability = $baseAvailability;

        return $this;
    }

    /**
     * Get baseAvailability
     *
     * @return integer 
     */
    public function getBaseAvailability()
    {
        return $this->baseAvailability;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Category
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
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
     * Add dates
     *
     * @param \AppBundle\Entity\TourDate $dates
     * @return Category
     */
    public function addDate(\AppBundle\Entity\TourDate $dates)
    {
        $this->dates[] = $dates;

        return $this;
    }

    /**
     * Remove dates
     *
     * @param \AppBundle\Entity\TourDate $dates
     */
    public function removeDate(\AppBundle\Entity\TourDate $dates)
    {
        $this->dates->removeElement($dates);
    }

    /**
     * Get dates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDates()
    {
        return $this->dates;
    }
}
