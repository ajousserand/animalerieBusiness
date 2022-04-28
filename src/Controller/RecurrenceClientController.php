<?php

namespace App\Controller;

use App\Repository\CommandRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class RecurrenceClientController extends AbstractController
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
        

        $commandNewClientEntities = $this->commandRepository->getCountCommandeNewClient($minDate,$maxDate);
        $commandOldClientEntities = $this->commandRepository->getCountCommandeOldClient($minDate,$maxDate);

       
        if(count($commandNewClientEntities)!= 0 && count($commandOldClientEntities)!= 0){
            return $this->json(['data'=>number_format((count($commandNewClientEntities)/count($commandOldClientEntities))*100,2)]);
        }else{
            return($this->json(['data'=>0]));
        }
    }
   
}
