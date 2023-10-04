<?php

namespace App\Component\LoyaltyPoints;

use App\Entity\User;

class Calculator implements LoyaltyPointsCalculatorInterface
{
    public final const PRICING_PLANS_TABLE = [
        0 => 5, // Sunday
        1 => 3, // Monday
        2 => 3, // Tuesday
        3 => 3, // Wednesday
        4 => 3, // Thursday
        5 => 4, // Friday
        6 => 5  // Saturday
    ];

    public function calculateLoyaltyPoints(string $pricingPlanName, int $loyaltyPoints, string $dayOfWeek = null): int
    {
        return $loyaltyPoints * $this->getLoyaltyPointsMultiplier($pricingPlanName, $dayOfWeek ?? date('w'));
    }

    public function getLoyaltyPointsMultiplier(string $pricingPlanName, string $dayOfWeek = null): int
    {
        return match ($pricingPlanName) {
            User::PRICING_PLAN_DEFAULT => 1,
            User::PRICING_PLAN_PREMIUM => self::PRICING_PLANS_TABLE[$dayOfWeek ?? date('w')],
            default => throw new \RuntimeException('Incorrect loyalty points plan name.'),
        };
    }
}