<?php

namespace App\Security\Voter;

use App\Entity\Utilisateurs;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class RoleVoter extends Voter
{
    const CHANGER_ROLES = 'CHANGER_ROLES';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // Only vote on `Utilisateurs` objects and the `CHANGER_ROLES` attribute
        return $attribute === self::CHANGER_ROLES && $subject instanceof Utilisateurs;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Utilisateurs) {
            // The user must be logged in; if not, deny access
            return false;
        }

        // You know $subject is a `Utilisateurs` object, thanks to `supports()`
        /** @var Utilisateurs $targetUser */
        $targetUser = $subject;

        switch ($attribute) {
            case self::CHANGER_ROLES:
                return $this->canChangeRoles($user, $targetUser);
        }

        return false;
    }

    private function canChangeRoles(Utilisateurs $user, Utilisateurs $targetUser): bool
    {
        // Admins cannot change roles of other admins
        if (in_array('ROLE_ADMIN', $targetUser->getRoles()) && in_array('ROLE_ADMIN', $user->getRoles())) {
            return false;
        }

        // Allow role change if the user has ROLE_ADMIN
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}