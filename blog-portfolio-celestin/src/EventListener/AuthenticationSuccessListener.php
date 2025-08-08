<?php

namespace App\EventListener;

use App\Service\MercureCookieGenerator;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
// use Symfony\Component\Serializer\SerializerInterface;

class AuthenticationSuccessListener
{

    private $cookieGenerator;
    // private $serializer;

    public function __construct(MercureCookieGenerator $cookieGenerator)
    {
        $this->cookieGenerator = $cookieGenerator;
        // $this->serializer = $serializer;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        // if (!$user instanceof UserInterface) {
        //     return;
        // }

        // $json = $this->serializer->serialize(
        //     $user,
        //     'json', ['groups' => ['user_private', 'user_volunteer', 'user_student', 'volunteer_private', 'student_private', 'skill_private', 'need_private']]
        // );

        // $data['user'] = $json;

        // $data['data'] = array(
        //     'roles' => $user->getRoles(),
        // );
        $data['user'] = array(
            'id' => $user->getId(),
            'roles' => $user->getRoles(),
        );
        $data['mercureUserToken'] = $this->cookieGenerator->generate($user);

        $event->setData($data);
    }
}