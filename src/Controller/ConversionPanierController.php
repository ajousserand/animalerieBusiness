<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use App\Repository\VisiteRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class ConversionPanierController extends AbstractController
{
    private CommandRepository $commandRepository;
    private VisiteRepository $visiteRepository;

    public function __construct(CommandRepository $commandRepository,VisiteRepository $visiteRepository)
    {
        $this->commandRepository = $commandRepository;
        $this->visiteRepository = $visiteRepository;
    }
    public function __invoke(Request $request)
    {
        $minDateString = $request->query->get(key:'min_date');
        $maxDateString = $request->query->get(key:'max_date');

        $minDate = new DateTime ($minDateString);
        $maxDate = new DateTime($maxDateString);
        

        $commandEntities = $this->commandRepository->getCountPanier($minDate,$maxDate);
        $countVisit = $this->visiteRepository->getVisitBetweenDate($minDate,$maxDate);

        dump(count($countVisit));
        dump(count($commandEntities));

        if(count($countVisit)!= 0 && count($commandEntities)!= 0){
            return $this->json(number_format(count($countVisit)/count($commandEntities)*100,2));
        }else{
            return($this->json(0));
        }
    }
   
}