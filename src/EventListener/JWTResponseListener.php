<?php

declare(strict_types=1);

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class JWTResponseListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $token = $data['token'];

        $response = $event->getResponse();
        $response->headers->setCookie(
            new Cookie(
                '__Host-JWT',
                $token,
                (new \DateTime())->modify('+1 hour'),
                '/',
                null,
                true,
                true,
                false,
                'Strict'
            )
        );

        $event->setData($data);
    }
}
