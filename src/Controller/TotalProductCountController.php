<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TotalProductCountController extends AbstractController
{
    private CommandRepository $commandRepository;

    public function __construct(CommandRepository $commandRepository)
    {
        $this->commandRepository = $commandRepository;
    }
    public function __invoke(Request $request)
    {
        $minDateString = $request->query->get(key:'min_date');
        $maxDateString = $request->query->get(key:'max_date');

        $minDate = new DateTime ($minDateString);
        $maxDate = new DateTime($maxDateString);
        

        $commandEntities = $this->commandRepository->getTotalVenteBetweenDate($minDate,$maxDate);

        $sum=0;
        foreach($commandEntities as $commandEntity){
            $sum +=$commandEntity->getTotalPrice();
        }


        if($commandEntities!=0){
            return $this->json($sum/count($commandEntities));
        }else;{
            return $this->json(0);
        }
    }
   
}
