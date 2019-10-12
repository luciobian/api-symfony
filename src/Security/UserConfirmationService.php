<?php

namespace App\Security;

use Monolog\Logger;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\InvalidConfirmationTokenException;

class UserConfirmationService 
{

    private $userRepository;

    private $entityManager;

    private $logger;
    
    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        Logger $logger)
    {   
        $this->userRepository = $userRepository;    
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function confirmUser(string $confirmationToken)
    {
        $user = $this->userRepository->findOneBy(
            ['confirmationToken' => $confirmationToken]
        );

        // User was NOT found by confirmation token
        if (!$user) {
            throw new InvalidConfirmationTokenException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();

    }

    
}