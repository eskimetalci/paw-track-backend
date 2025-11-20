<?php

namespace App\Security\Voter;

use App\Entity\Animal;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AnimalVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Only vote on Animal objects
        if (!$subject instanceof Animal) {
            return false;
        }

        // Only vote on these attributes
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // User must be logged in
        if (!$user instanceof User) {
            return false;
        }

        /** @var Animal $animal */
        $animal = $subject;

        // Check if the user owns this animal
        return match ($attribute) {
            self::VIEW => $this->canView($animal, $user),
            self::EDIT => $this->canEdit($animal, $user),
            self::DELETE => $this->canDelete($animal, $user),
            default => false,
        };
    }

    private function canView(Animal $animal, User $user): bool
    {
        // Users can only view their own animals
        return $animal->getOwner() === $user;
    }

    private function canEdit(Animal $animal, User $user): bool
    {
        // Users can only edit their own animals
        return $animal->getOwner() === $user;
    }

    private function canDelete(Animal $animal, User $user): bool
    {
        // Users can only delete their own animals
        return $animal->getOwner() === $user;
    }
}

