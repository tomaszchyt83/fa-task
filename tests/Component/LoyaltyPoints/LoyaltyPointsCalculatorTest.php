<?php

namespace App\Tests\Component\LoyaltyPoints;

use App\Component\LoyaltyPoints\Calculator;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class LoyaltyPointsCalculatorTest extends TestCase
{
    private Calculator $calculator;

    public function setUp(): void {
        $this->calculator = new Calculator();
    }

    public function testCalculatePremiumLoyaltyPointsOnMonday(): void {
        $this->assertEquals(30, $this->calculator->calculateLoyaltyPoints(User::PRICING_PLAN_PREMIUM, 10, 1));
    }

    public function testCalculatePremiumLoyaltyPointsOnTuesday(): void {
        $this->assertEquals(30, $this->calculator->calculateLoyaltyPoints(User::PRICING_PLAN_PREMIUM, 10, 2));
    }
    public function testCalculatePremiumLoyaltyPointsOnWednesday(): void {
        $this->assertEquals(30, $this->calculator->calculateLoyaltyPoints(User::PRICING_PLAN_PREMIUM, 10, 3));
    }
    public function testCalculatePremiumLoyaltyPointsOnThursday(): void {
        $this->assertEquals(30, $this->calculator->calculateLoyaltyPoints(User::PRICING_PLAN_PREMIUM, 10, 4));
    }
    public function testCalculatePremiumLoyaltyPointsOnFriday(): void {
        $this->assertEquals(40, $this->calculator->calculateLoyaltyPoints(User::PRICING_PLAN_PREMIUM, 10, 5));
    }
    public function testCalculatePremiumLoyaltyPointsOnSaturday(): void {
        $this->assertEquals(50, $this->calculator->calculateLoyaltyPoints(User::PRICING_PLAN_PREMIUM, 10, 6));
    }
    public function testCalculatePremiumLoyaltyPointsOnSunday(): void {
        $this->assertEquals(50, $this->calculator->calculateLoyaltyPoints(User::PRICING_PLAN_PREMIUM, 10, 0));
    }

    public function testGetPremiumLoyaltyPointsMultiplierOnMondayToThursday(): void {
        $this->assertEquals(3, $this->calculator->getLoyaltyPointsMultiplier(User::PRICING_PLAN_PREMIUM, 1));
        $this->assertEquals(3, $this->calculator->getLoyaltyPointsMultiplier(User::PRICING_PLAN_PREMIUM, 2));
        $this->assertEquals(3, $this->calculator->getLoyaltyPointsMultiplier(User::PRICING_PLAN_PREMIUM, 3));
        $this->assertEquals(3, $this->calculator->getLoyaltyPointsMultiplier(User::PRICING_PLAN_PREMIUM, 4));
    }

    public function testGetPremiumLoyaltyPointsMultiplierOnFriday(): void {
        $this->assertEquals(4, $this->calculator->getLoyaltyPointsMultiplier(User::PRICING_PLAN_PREMIUM, 5));
    }

    public function testGetPremiumLoyaltyPointsMultiplierOnWeekend(): void {
        $this->assertEquals(5, $this->calculator->getLoyaltyPointsMultiplier(User::PRICING_PLAN_PREMIUM, 6));
        $this->assertEquals(5, $this->calculator->getLoyaltyPointsMultiplier(User::PRICING_PLAN_PREMIUM, 0));
    }
}