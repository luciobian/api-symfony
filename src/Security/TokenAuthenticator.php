<?php 

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;

class TokenAuthenticator extends JWTTokenAuthenticator
{

    public function getUser($preAuthToken, \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider)
    {
        $user = parent::getUser(
            $preAuthToken,
            $userProvider
        );

        if ($user->getPasswordChangeDate() && $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate())
        {
            throw new ExpiredTokenException();
        }
        return $user; 
    }

}