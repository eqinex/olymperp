<?php

namespace AppBundle\Controller;

use AppBundle\Repository\RepositoryAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CalendarController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * @Route("/calendar", name="calendar_index")
     */
    public function listAction(Request $request)
    {
        $productionCalendarDays = $this->getProductionCalendarRepository()->findAll();

        return $this->render('calendar/list.html.twig', [
            'productionCalendarDays' => $productionCalendarDays
        ]);
    }
}
