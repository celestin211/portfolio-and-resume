<?php

namespace App\Service;


use App\Entity\Utilisateur;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

use Symfony\Component\HttpFoundation\Cookie;

class MercureCookieGenerator {

    private $secretKey;

    public function __construct(string $secretKey) {
        $this->secretKey = $secretKey;
    }

    public function generate(Utilisateur $user): string {
        $token = (new Builder())
            ->withClaim('mercure', ['subscribe' => ["https://esteam-asso.com/users/{$user->getId()}"]])
            ->sign(new Sha256(), $this->secretKey)
            ->getToken();
        return $token;
        // return "mercureAuthorization={$token}; Path=/.well-known/mercure; ; domain=http://localhost:4200; HttpOnly";
        // return Cookie::create('mercureAuthorization', $token, 0, '');
    }

}