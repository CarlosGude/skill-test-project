<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const PUT = 'PUT';
    public const POST = 'POST';
    public const DELETE = 'DELETE';

    public function __construct(protected Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::POST, self::PUT, self::DELETE], true) && $subject instanceof User;
    }

    /**
     * @param User $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();

        return match ($attribute) {
            self::POST => true,
            self::PUT => $user === $subject,
            self::DELETE => $this->security->isGranted(User::ROLE_ADMIN) && $subject !== $user,
            default => false,
        };
    }
}
