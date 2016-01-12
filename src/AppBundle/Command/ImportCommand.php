<?php
/**
 * Created by PhpStorm.
 * User: alhorro
 * Date: 09.12.2015
 * Time: 11:32
 */

namespace AppBundle\Command;


use AppBundle\Entity\Destination;
use AppBundle\Entity\TourDate;
use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use Gufy\PdfToHtml\Pdf;
use Gufy\PdfToHtml\Config;


class ImportCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('pdf:import')
            ->setDescription('Import PDFs')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $this->importAll($output);
        $output->writeln('Done.');
    }



    public function importAll(OutputInterface $output){
        //Config::set('pdftohtml.bin', 'D:/downloads/poppler/bin/pdftohtml.exe');
        //Config::set('pdfinfo.bin', 'D:/downloads/poppler/bin/pdfinfo.exe');

        foreach($this->getContainer()->get('doctrine')->getRepository('AppBundle:Destination')->findAll() as $dest){
            $date = new Carbon();
            $output->writeln('Importing '.$dest->getName().' for '.$date.'...');
            $this->import($dest, $date->month, $date->year);

            $cont = true;
            while($cont) {
                $date->month += 1;
                $output->writeln('Importing ' . $dest->getName() . ' for ' . $date . '...');
                $cont = $this->import($dest, $date->month, $date->year);
            }
        }
    }

    public function import(Destination $dest, $month, $year){
        $url    = 'http://www.machupicchu.gob.pe/rpt//DisponibilidadPorMes.cfm?idLugar='.$dest->getId().'&mes='.$month.'&ano='.$year;
        $fn     = __DIR__.'/d.pdf';
        $return = true;
        
        file_put_contents($fn, file_get_contents($url));
        $pdf = new Pdf($fn);

        $paragraphs = $pdf->html()->find('p.ft03');
        $ret        = array();
        $curr       = 0;

        foreach($paragraphs as $i=>$p) {
            if($i==0) continue;
            
            if ($i % 2 != 0){
                $curr = $p->text();
            }else{
                $ret[$curr] = $p->text();
            }
        }

        if ( empty($ret) ) {
            $return = false;
            $date = Carbon::createFromDate($year, $month, 1);
            $lastDayofMonth = $date->format('t') + 1;

            $ret = array();

            for ($i=1; $i < $lastDayofMonth; $i++) { 
                $ret[$i] = 0;
            }
        }

        echo('<pre>');var_dump($ret);

        foreach($ret as $day => $availability){
            $date = Carbon::createFromDate($year, $month, $day);

            $e = $this->getContainer()->get('doctrine')->getRepository('AppBundle:TourDate')
                ->findOneBy(array('date' => $date, 'destination' => $dest));

            if(empty($e)){
                $e = new TourDate();
                $e->setDestination($dest);
                $e->setDate($date);
            }

            $e->setAvailability($availability);
            
            $this->getContainer()->get('doctrine')->getEntityManager()->persist($e);
            $this->getContainer()->get('doctrine')->getEntityManager()->flush();
        }
        
        return $return;
    }

}