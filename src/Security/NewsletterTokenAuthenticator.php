<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class NewsletterTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->query->has('newsletterToken');
    }

    public function authenticate(Request $request): Passport
    {
        return new SelfValidatingPassport(
            new UserBadge(
                $request->query->get('newsletterToken'),
                fn (string $token) => $this->userRepository->findOneBy(['newsletterToken' => $token]),
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $url = parse_url($request->getRequestUri(), PHP_URL_PATH);
        $query = $request->query->all();
        unset($query['newsletterToken']);

        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        return new RedirectResponse($url);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
