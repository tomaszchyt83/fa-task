<?php

namespace App\Component\LoyaltyPoints;

interface LoyaltyPointsCalculatorInterface
{
    public function calculateLoyaltyPoints(string $pricingPlanName, int $loyaltyPoints): int;

    public function getLoyaltyPointsMultiplier(string $pricingPlanName): int;
}