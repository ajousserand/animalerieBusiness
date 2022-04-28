<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class ConversionCommandController extends AbstractController
{
    private CommandRepository $commandRepository;

    public function __construct(CommandRepository $commandRepository)
    {
        $this->commandRepository = $commandRepository;
    }
    
    public function __invoke(Request $request)
    {
        dump($this->getUser());
        $minDateString = $request->query->get(key:'min_date');
        $maxDateString = $request->query->get(key:'max_date');

        $minDate = new DateTime ($minDateString);
        $maxDate = new DateTime($maxDateString);
        
       

        $panierEntities = $this->commandRepository->getCountPanier($minDate,$maxDate);
        $commandEntities = $this->commandRepository->getCountCommandBetweenDate($minDate,$maxDate);

        if(count($panierEntities)!= 0 && count($commandEntities)!= 0){
            return $this->json(number_format(count($panierEntities)/count($commandEntities)*100,2));
        }else{
            return($this->json(0));
        }
    }
   
}