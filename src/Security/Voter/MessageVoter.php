<?php

namespace App\Security\Voter;

use App\Entity\Message;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class MessageVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return str_starts_with($attribute, 'MESSAGE_');
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        if ('MESSAGE_EDIT' === $attribute) {
            if (!$subject instanceof Message) {
                throw new \LogicException(\sprintf('The "%s" attribute is only supported for instances of "%s", got "%s".', $attribute, Message::class, get_debug_type($subject)));
            }

            return $user === $subject->author;
        }

        throw new \LogicException(\sprintf('The "%s" attribute is not supported.', $attribute));
    }
}
