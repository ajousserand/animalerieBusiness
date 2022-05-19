<?php

namespace App\Service;

use App\Entity\Command;
use App\Entity\Product;
use App\Entity\User;
use App\Enum\EnumCommand;
use App\Repository\CommandRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class BasketService{

    public function __construct(private CommandRepository $commandRepository,private EntityManagerInterface $em)
    {
        
    }
    public function getbasket(User $user):Command{
        $basketEntity = $this->commandRepository->getBasketByUser($user);
        if($basketEntity===null){
            $basketEntity = new Command();
            $basketEntity->setStatus(EnumCommand::BASKET_SIMPLE);
            $basketEntity->setUser($user);
            $basketEntity->setCreatedAt(new DateTime('now'));
            $basketEntity->setNumCommand(uniqid());
            $basketEntity->setTotalPrice(0);
            $basketEntity->setAddress($user->getAddresses()[0]);
            $this->em->persist($basketEntity);
            $this->em->flush();

        }
        return $basketEntity;
    }

    public function addProductToBasket(Product $product, User $user){
        
        $basketEntity = $this->getbasket($user);
        $basketEntity->addProduct($product);
        $basketEntity->setTotalPrice($product->getPrice()+$basketEntity->getTotalPrice());
        
        $this->em->persist($basketEntity);
        $this->em->flush();

    }

    public function removeProductToBasket(Product $product, User $user){

        $basketEntity = $this->getbasket($user);
        $basketEntity->removeProduct($product);
        $basketEntity->setTotalPrice($basketEntity->getTotalPrice()-$product->getPrice());

        $this->em->persist($basketEntity);
        $this->em->flush();

    }


}

