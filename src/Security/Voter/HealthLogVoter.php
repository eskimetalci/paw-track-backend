<?php

namespace App\Security\Voter;

use App\Entity\Animal;
use App\Entity\MedicineLog;
use App\Entity\PooLog;
use App\Entity\User;
use App\Entity\VaccineLog;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class HealthLogVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Only vote on health log objects
        if (!$this->isHealthLog($subject)) {
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

        // Get the animal from the log
        $animal = $this->getAnimalFromLog($subject);
        
        if (!$animal) {
            return false;
        }

        // Check if the user owns the animal associated with this log
        return match ($attribute) {
            self::VIEW => $this->canView($animal, $user),
            self::EDIT => $this->canEdit($animal, $user),
            self::DELETE => $this->canDelete($animal, $user),
            default => false,
        };
    }

    private function isHealthLog(mixed $subject): bool
    {
        return $subject instanceof PooLog 
            || $subject instanceof MedicineLog 
            || $subject instanceof VaccineLog;
    }

    private function getAnimalFromLog(mixed $log): ?Animal
    {
        if ($log instanceof PooLog) {
            return $log->getAnimal();
        }
        
        if ($log instanceof MedicineLog) {
            return $log->getAnimal();
        }
        
        if ($log instanceof VaccineLog) {
            return $log->getAnimal();
        }

        return null;
    }

    private function canView(Animal $animal, User $user): bool
    {
        // Users can only view logs for their own animals
        return $animal->getOwner() === $user;
    }

    private function canEdit(Animal $animal, User $user): bool
    {
        // Users can only edit logs for their own animals
        return $animal->getOwner() === $user;
    }

    private function canDelete(Animal $animal, User $user): bool
    {
        // Users can only delete logs for their own animals
        return $animal->getOwner() === $user;
    }
}

