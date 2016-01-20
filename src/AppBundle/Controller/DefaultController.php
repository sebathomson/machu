<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\TourDate;
use Carbon\Carbon;
use Gufy\PdfToHtml\Pdf;
use Gufy\PdfToHtml\Config;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        $cats = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        return $this->render('default/index.html.twig', array(
            'cats'=>$cats,
            ));
    }

    /**
     * @Route("/category/{id}", name="category")
     */
    public function categoryAction($id)
    {
        $request   = $this->get('request');
        $year      = $request->query->get('year', Date('Y'));
        $month     = $request->query->get('month', Date('m'));

        $today     = new \DateTime();
        $iToday    = intval( $today->format('Ymd') );

        $date    = Date('d') . '-' . $month . '-' . $year;
        $date    = new \DateTime($date);

        $prevCalendar = clone $date;
        $prevCalendar = $prevCalendar->modify('-1 months');
        $nextCalendar = clone $date;
        $nextCalendar = $nextCalendar->modify('+1 months');

        $cat = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        if ( $cat->getDestination()->getId() == 10 ) {
            // Custom Destination

            $template  = 'default/categoryCustom.html.twig';
            $datesTour = $cat->getDates();

            $arrDates  = array();

            foreach ($datesTour as $key => $value) {
                if ( $value->getDate()->format('Y') == $year ) {
                    if ( $value->getDate()->format('m') == $month ) {
                        $reservations = $this->getCountReservationsApproved( $value->getReservations() );

                        $arrDates[ intval($value->getDate()->format('d')) ] = array(
                            'id'           => $value->getId(),
                            'availability' => $value->getAvailability() - $reservations,
                            'reservations' => $reservations,
                            'users'        => $this->getReservationsApproved( $value->getReservations() ),
                            'isFullReserved' => ( (($value->getAvailability() - $reservations) == 0) AND ($value->getAvailability() != 0) )? true: false
                            );
                    }
                }
            }

            if (empty($arrDates)) {
                $date = Carbon::createFromDate($year, $month, 1);
                $lastDayofMonth = $date->format('t') + 1;

                for ($i=1; $i < $lastDayofMonth; $i++) { 
                    $arrDates[$i] = array(
                        'availability' => 0,
                        'reservations' => 0,
                        'users'        => array(),
                        );
                }
            }
        }

        return $this->render($template, array(
            'id'           => $cat->getId(),
            'date'         => $date,
            'iToday'       => $iToday,
            'arrDates'     => $arrDates,
            'nameCategory' => $cat->getName(),
            'prevCalendar' => $prevCalendar,
            'nextCalendar' => $nextCalendar,
            )
        );
    }

    /**
     * @Route("/manage/reservation/{reservationId}/approve", name="approve_reservation")
     */
    public function approveReservationAction($reservationId){
        $r = $this->getDoctrine()->getRepository('AppBundle:Reservation')->find($reservationId);
        $r->setStatus('approved');
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->redirectToRoute('admin_app_reservation_edit', array(
            '_sonata_admin' => 'admin.reservation',
            '_sonata_name' => 'admin_app_reservation_edit',
            'id' => $reservationId,
            ));
    }

    /**
     * @Route("/manage/reservation/{reservationId}/reject", name="reject_reservation")
     */
    public function rejectReservationAction($reservationId){
        $r = $this->getDoctrine()->getRepository('AppBundle:Reservation')->find($reservationId);
        $r->setStatus('rejected');
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->redirectToRoute('admin_app_reservation_edit', array(
            '_sonata_admin' => 'admin.reservation',
            '_sonata_name' => 'admin_app_reservation_edit',
            'id' => $reservationId,
            ));
    }

    /**
     * @Route("/reservation/{tourDateId}", name="reservation")
     */
    public function reservationAction($tourDateId, Request $request) {
        $r = new Reservation();
        $r->setTourDate($this->getDoctrine()->getRepository('AppBundle:TourDate')->find($tourDateId));
        $form = $this->createFormBuilder($r)
        ->add('name')
        ->add('email', 'email')
        ->add('country', 'country')
        ->add('submit', 'submit')
        ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $r->setStatus('pending');
            $this->getDoctrine()->getEntityManager()->persist($r);
            $this->getDoctrine()->getEntityManager()->flush();


            $message = \Swift_Message::newInstance()
            ->setSubject('New reservation')
            ->setFrom('no-reply@southadventureperutours.com')
            // ->setTo('info@southadventureperutours.com')
            // ->setTo('seba.thomson@gmail.com')
            ->setTo('nestormq@gmail.com')
            ->setBody(
                $this->renderView(
                    'email/notification.html.twig',
                    array('r'=>$r)
                    ),
                'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            return $this->render('default/reservation_success.html.twig', array('r'=>$r));
        }
        return $this->render('default/reservation.html.twig', array('form'=>$form->createView()));
    }

    public function getCountReservationsApproved($reservations)
    {
        $count = 0;

        foreach ($reservations as $r) {
            if($r->getStatus() == 'approved'){
                $count ++;
            }
        }

        return $count;
    }

    public function getReservationsApproved($reservations)
    {
        $return = array();

        foreach ($reservations as $r) {
            if($r->getStatus() == 'approved'){
                $return[] = $r;
            }
        }

        return $return;
    }

}
