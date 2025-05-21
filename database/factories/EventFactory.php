<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'name_event' => 'Test Event',
            'type_event' => 'Conference',
            'place_event' => 'Hall A',
            'location_event' => 'Test City',
            'start_event' => now(),
            'end_event' => now()->addDay(),
            'information_event' => 'Test info',
            'code_event' => 'TST123',
        ];
    }
}

