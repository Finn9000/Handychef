<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // The one and only admin account — never created via public registration.
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@handychef.test',
            'role' => User::ROLE_ADMIN,
        ]);

        // A sample customer for quick manual testing.
        $customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@handychef.test',
            'role' => User::ROLE_CUSTOMER,
            'age' => 28,
            'address' => '45 Customer Lane, Phnom Penh',
        ]);

        // A sample ghost kitchen for quick manual testing.
        $kitchenUser = User::factory()->create([
            'name' => 'Test Kitchen Owner',
            'email' => 'kitchen@handychef.test',
            'role' => User::ROLE_GHOST_KITCHEN,
        ]);

        $kitchen = $kitchenUser->ghostKitchen()->create([
            'business_name' => 'Test Ghost Kitchen',
            'manager_name' => 'Alex Kim',
            'description' => 'A sample kitchen for testing.',
            'address' => '123 Kitchen Row, Phnom Penh',
            'phone' => '012345678',
            'status' => 'approved',
        ]);

        // A sample meal plan with a few meal items.
        $plan = $kitchen->mealPlans()->create([
            'name' => 'Weekly Balanced Plan',
            'description' => '5 balanced meals a week, picked up daily.',
            'price' => 49.99,
            'meals_per_week' => 5,
            'is_active' => true,
        ]);

        $items = [
            ['name' => 'Grilled Chicken & Rice', 'description' => 'Grilled chicken breast with jasmine rice and veggies.'],
            ['name' => 'Beef Stir Fry', 'description' => 'Beef strips stir-fried with mixed vegetables.'],
            ['name' => 'Salmon & Quinoa', 'description' => 'Pan-seared salmon with quinoa and greens.'],
        ];

        foreach ($items as $item) {
            $plan->mealItems()->create($item);
        }

        // Give the sample customer an active subscription to the sample plan.
        $subscription = $customer->subscriptions()->create([
            'meal_plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => now(),
            'pickup_time' => \App\Models\Subscription::PICKUP_TIME_SLOTS[1],
            'pickup_location' => 'Toul Kork - ABC Mart, Phnom Penh',
            'pickup_latitude' => 11.5750,
            'pickup_longitude' => 104.8921,
        ]);

        // Customer picks the first two meal items for their subscription.
        $subscription->subscriptionItems()->createMany(
            $plan->mealItems->take(2)->map(fn ($item) => ['meal_item_id' => $item->id])->all()
        );

        // Seed today's pickup schedule entry for that subscription.
        $subscription->pickupSchedules()->create([
            'pickup_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }
}
