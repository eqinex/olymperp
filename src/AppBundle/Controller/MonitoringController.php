<?php
/**
 * Created by PhpStorm.
 * User: mazitovtr
 * Date: 13.03.19
 * Time: 14:23
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Monitoring;
use AppBundle\Entity\MonitoringHostname;
use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MonitoringController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("/monitoring", name="monitoring")
     */
    public function monitoringAction(Request $request)
    {
        $contents = json_decode($request->getContent(), true);

        $em = $this->getDoctrine()->getEntityManager();

        /** @var MonitoringHostname $hostname */
        $hostname = $this->getMonitoringHostnameRepository()->findOneBy(['hostname' => $contents['hostname']]);

        if (!$hostname) {
            $hostname = new MonitoringHostname();

            $hostname->setHostname($contents['hostname']);

            $em->persist($hostname);
            $em->flush();
        }

        $unitTotal = substr($contents['total'], strlen($contents['total']) - 1, strlen($contents['total']));
        $unitFree = substr($contents['free'], strlen($contents['free']) - 1, strlen($contents['free']));

        $total = substr($contents['total'], 0, strlen($contents['total']) - 1);
        $free = substr($contents['free'], 0, strlen($contents['free']) - 1);

        $unitTotal != 'T' ? : $total *= 1000;
        $unitFree != 'T' ?  : $free *= 1000;

        $loadAverageMinute = substr($contents['uptime'], strlen($contents['uptime']) - 16, 4);
        $loadAverageMinute = str_replace(',', '.', $loadAverageMinute);

        $loadAverageFiveMinutes = substr($contents['uptime'], strlen($contents['uptime']) - 10, 4);
        $loadAverageFiveMinutes = str_replace(',', '.', $loadAverageFiveMinutes);

        $loadAverageFifteenMinutes = substr($contents['uptime'], strlen($contents['uptime']) - 4, 4);
        $loadAverageFifteenMinutes = str_replace(',', '.', $loadAverageFifteenMinutes);
        $content = $contents['uptime'];

        if (preg_match('/days?/i', $content)) {

            $pattern = "/up\ +(\d+)\ +days?/i";
            $days = $this->getUptime($pattern, $content);
            $daysInMinutes = (24 * 60) * $days;

            if (preg_match('/min/i', $content)) {
                $pattern = "/\ +(\d+)\ +min/i";
                $minutes = $this->getUptime($pattern, $content);
                $uptime = $daysInMinutes + $minutes;
            } else {
                $pattern = "/days?,\ +(\d+)\:/i";
                $hours = $this->getUptime($pattern, $content);
                $hoursInMinutes = $hours * 60;

                $pattern = "/\:(\d+)\,/i";
                $minutes = $this->getUptime($pattern, $content);

                $uptime = $daysInMinutes + $hoursInMinutes + $minutes;
            }

        } elseif (preg_match('/min/i', $content)) {

            $pattern = "/up\ +(\d+)\ +min/i";
            $uptime = $this->getUptime($pattern, $content);

        } else {

            $pattern = "/up\ +(\d+)\:/i";
            $hours = $this->getUptime($pattern, $content);
            $hoursInMinutes = $hours * 60;

            $pattern = "/\:(\d+)\,/i";
            $minutes = $this->getUptime($pattern, $content);

            $uptime = $hoursInMinutes + $minutes;
        }

        $server = new Monitoring();

        $server
            ->setDisk($contents['disk'])
            ->setTotal($total)
            ->setFree($free)
            ->setHostname($hostname)
            ->setMemtotal($contents['memtotal'])
            ->setMemavail($contents['memavail'])
            ->setCreatedAt(new \DateTime(date('d.m.Y H:i')))
            ->setLoadAverageMinute($loadAverageMinute)
            ->setLoadAverageFiveMinutes($loadAverageFiveMinutes)
            ->setLoadAverageFifteenMinutes($loadAverageFifteenMinutes)
            ->setUptime($uptime)
        ;

        $em->persist($server);
        $em->flush();

        return new Response($request->getContent());
    }

    /**
     * @param $pattern
     * @param $content
     * @return mixed
     */
    protected function getUptime($pattern, $content)
    {
        preg_match($pattern, $content, $matches);

        return $matches[1];
    }

    /**
     * @Route("/monitoring-servers", name="monitoring_servers_list")
     */
    public function listAction(Request $request)
    {
        if (!$this->getUser()->canViewInfrastructure()) {
            return $this->redirectToRoute('homepage');
        }

        $hostnames = $this->getMonitoringHostnameRepository()->findAll();

        return $this->render('monitoring/list.html.twig', [
            'hostnames' => $hostnames
        ]);
    }
}