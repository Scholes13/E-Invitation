<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'name_app' => 'Test App',
            'send_email' => true,
            'send_whatsapp' => false,
            'greeting_page' => true,
            'enable_rsvp' => true,
            'rsvp_deadline' => now()->addWeek(),
            'enable_plus_ones' => true,
            'collect_dietary_preferences' => false,
            'send_rsvp_reminders' => false,
            'reminder_days_before_deadline' => 3,
        ];
    }
}

