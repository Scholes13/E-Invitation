<?php

namespace Database\Factories;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        $code = Str::random(6);

        return [
            'name_guest' => $this->faker->name(),
            'email_guest' => $this->faker->unique()->safeEmail(),
            'phone_guest' => '0812345678',
            'created_by_guest' => 'factory',
            'qrcode_invitation' => $code,
            'type_invitation' => 'reguler',
            'link_invitation' => '/invitation/' . $code,
            'image_qrcode_invitation' => '/img/qrCode/' . $code . '.png',
            'id_event' => 1,
        ];
    }
}

