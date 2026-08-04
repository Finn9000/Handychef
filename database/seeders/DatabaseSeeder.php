<?php

namespace Database\Seeders;

use App\Models\MealItemIngredientOption;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
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
        // A sample customer for quick manual testing.
        $customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@handychef.test',
            'role' => User::ROLE_CUSTOMER,
            'phone' => '098765432',
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
        ]);

        // A sample meal plan with a few meal items.
        $plan = $kitchen->mealPlans()->create([
            'name' => 'Weekly Balanced Plan',
            'description' => '5 balanced meals a week, picked up daily.',
            'price' => 49.99,
            'available_days' => ['mon', 'tue', 'wed', 'thu'],
            'is_active' => true,
        ]);

        $items = [
            ['name' => 'Grilled Chicken & Rice', 'description' => 'Grilled chicken breast with jasmine rice and veggies.'],
            ['name' => 'Beef Stir Fry', 'description' => 'Beef strips stir-fried with mixed vegetables.'],
            ['name' => 'Salmon & Quinoa', 'description' => 'Pan-seared salmon with quinoa and greens.'],
        ];

        $mealItems = collect($items)->map(fn ($item) => $plan->mealItems()->create($item));

        // Sample kitchen-defined ingredient options on the first meal item.
        $chickenItem = $mealItems->first();
        $extraChicken = $chickenItem->ingredientOptions()->create([
            'name' => 'Extra Chicken',
            'type' => MealItemIngredientOption::TYPE_ADD,
            'price_delta' => 2.00,
        ]);
        $chickenItem->ingredientOptions()->create([
            'name' => 'Remove Onions',
            'type' => MealItemIngredientOption::TYPE_REMOVE,
            'price_delta' => 0,
        ]);

        // Give the sample customer an active subscription to the sample plan.
        $subscription = $customer->subscriptions()->create([
            'meal_plan_id' => $plan->id,
            'status' => Subscription::STATUS_ACTIVE,
            'started_at' => now(),
            'pickup_time' => Subscription::PICKUP_TIME_SLOTS[1],
            'pickup_location' => 'Toul Kork - ABC Mart, Phnom Penh',
            'pickup_latitude' => 11.5750,
            'pickup_longitude' => 104.8921,
        ]);

        // Customer picks the chicken dish for morning (with an ingredient option)
        // and the salmon dish for evening.
        $morningItem = $subscription->subscriptionItems()->create([
            'meal_item_id' => $chickenItem->id,
            'slot' => SubscriptionItem::SLOT_MORNING,
        ]);
        $morningItem->ingredientOptions()->attach($extraChicken->id);

        $subscription->subscriptionItems()->create([
            'meal_item_id' => $mealItems->last()->id,
            'slot' => SubscriptionItem::SLOT_EVENING,
        ]);

        // Seed today's pickup schedule entry for that subscription.
        $subscription->pickupSchedules()->create([
            'pickup_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }
}
