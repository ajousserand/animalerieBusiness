<?php

namespace App\Controller;

use App\Repository\VisiteRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CountVisitController extends AbstractController
{
    private VisiteRepository $visiteRepository;

    public function __construct(VisiteRepository $visiteRepository)
    {
        $this->visiteRepository = $visiteRepository;
    }
     
    public function __invoke(Request $request)
    {
        // http://127.0.0.1:8000/ma-route?truc=aze
        // $truc = $request->query->get(key:'truc');
        // dump($truc);
        $minDateString = $request->query->get(key:'min_date');
        $maxDateString = $request->query->get(key:'max_date');

        $minDate = new DateTime ($minDateString);
        $maxDate = new DateTime($maxDateString);
        

        $visitEntities = $this->visiteRepository->getVisitBetweenDate($minDate,$maxDate);

        dump($visitEntities);

        return $this->json(['data'=>count($visitEntities)]);
    }
}
