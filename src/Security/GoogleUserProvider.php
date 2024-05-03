<?php
// src/Security/GoogleUserProvider.php
namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class GoogleUserProvider extends OAuthUserProvider
{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response): UserInterface
    {
        $username = $response->getUsername();

        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            $user = new OAuthUser($username);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }
}