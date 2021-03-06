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
 * @ORM\Table(name="reservation")
 * @UniqueEntity({"email", "tourDate"})
 */
class Reservation {

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
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $country;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\ManyToOne(targetEntity="TourDate", inversedBy="reservations")
     * @ORM\JoinColumn(name="tour_date_id", referencedColumnName="id")
     */
    protected $tourDate;

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
     * @return Reservation
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
     * Set email
     *
     * @param string $email
     * @return Reservation
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
     * Set country
     *
     * @param string $country
     * @return Reservation
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Reservation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set tourDate
     *
     * @param \AppBundle\Entity\TourDate $tourDate
     * @return Reservation
     */
    public function setTourDate(\AppBundle\Entity\TourDate $tourDate = null)
    {
        $this->tourDate = $tourDate;

        return $this;
    }

    /**
     * Get tourDate
     *
     * @return \AppBundle\Entity\TourDate 
     */
    public function getTourDate()
    {
        return $this->tourDate;
    }
}
