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
     * @Route("/category/{catId}", name="category")
     */
    public function categoryAction($catId)
    {
        $cat = $this->getDoctrine()->getRepository('AppBundle:Category')->find($catId);

        $minAv       = $cat->getMinAvailability();
        $isCustom    = false;
        $isAvailable = true;
        $today       = new \DateTime();
        $todayInt    = (int) $today->format('Ymd');

        if ( $cat->getDestination()->getId() == 10 ) {
            // Custom Destination
            $gav      = $cat->getCustomAvailability();
            $av       = $cat->getCustomAvailability();
            $d        = $cat->getCustomDate();
            $isCustom = true;
            $date     = $cat->getCustomDate();
        } else {
            $d    = $cat->getDate();
            $date = $cat->getDate()->getDate();
            $av   = floor($d->getAvailability()/2);
            $gav  = $d->getAvailability();
        }

        $dateInt    = (int) $date->format('Ymd');

        if ($todayInt > $dateInt) {
            $isAvailable = false;
        }

        $rcnt = 0;
        $unames = array();
        foreach($cat->getReservations() as $r){
            if($r->getStatus() == 'approved'){
                $rcnt ++;
                $unames[]= $r->getName();
            }
        }

        $av-=$rcnt;
        $na = false;

        if($av <= $minAv){
            $na = true;
        }

        return $this->render('default/category.html.twig', array(
            'gav'         => $gav,
            'cat'         => $cat,
            'td'          => $d,
            'av'          => $av,
            'rcnt'        => $rcnt,
            'unames'      => $unames,
            'isCustom'    => $isCustom,
            'isAvailable' => $isAvailable
        ));
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
     * @Route("/reservation/{catId}", name="reservation")
     */
    public function reservationAction($catId, Request $request) {
        $r = new Reservation();
        $r->setCategory($this->getDoctrine()->getRepository('AppBundle:Category')->find($catId));
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
                ->setTo('info@southadventureperutours.com')
                ->setBody(
                    $this->renderView(
                        'email/notification.html.twig',
                        array('r'=>$r)
                    ),
                    'text/html'
                )
                /*
                 * If you also want to include a plaintext version of the message
                ->addPart(
                    $this->renderView(
                        'Emails/registration.txt.twig',
                        array('name' => $name)
                    ),
                    'text/plain'
                )
                */
            ;
            $this->get('mailer')->send($message);

            return $this->render('default/reservation_success.html.twig', array('r'=>$r));
        }
        return $this->render('default/reservation.html.twig', array('form'=>$form->createView()));
    }


}
