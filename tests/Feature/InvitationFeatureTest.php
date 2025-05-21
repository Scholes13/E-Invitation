<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\Event;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InvitationFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function seedDefaults(): void
    {
        Event::factory()->create(['id_event' => 1]);
        Setting::factory()->create();
    }

    public function test_invitation_creation_generates_qr_file(): void
    {
        $this->seedDefaults();

        $user = User::factory()->create(['role' => 1, 'username' => 'admin']);
        $this->actingAs($user);

        $response = $this->post('/invite/store', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0812345678',
            'type' => 'reguler',
        ]);

        $response->assertRedirect('/invite');

        $invitation = Invitation::first();
        $this->assertNotNull($invitation);
        $this->assertFileExists(public_path('img/qrCode/' . $invitation->qrcode_invitation . '.png'));
    }

    public function test_guest_link_page_displays_invitation(): void
    {
        $this->seedDefaults();

        $invitation = Invitation::factory()->create();

        $response = $this->get('/invitation/' . $invitation->qrcode_invitation);
        $response->assertStatus(200);
        $response->assertSeeText($invitation->name_guest);
    }

    public function test_rsvp_flow(): void
    {
        $this->seedDefaults();

        $invitation = Invitation::factory()->create();

        $response = $this->get('/rsvp/guest/' . $invitation->qrcode_invitation);
        $response->assertStatus(200);

        $post = $this->post('/rsvp/guest/' . $invitation->qrcode_invitation, [
            'rsvp_status' => 'yes',
            'plus_ones_count' => 0,
        ]);
        $post->assertRedirect(route('rsvp.thank-you', ['qrcode' => $invitation->qrcode_invitation]));

        $thank = $this->get('/rsvp/thank-you/' . $invitation->qrcode_invitation);
        $thank->assertStatus(200);
        $thank->assertSeeText('thank');
    }

    public function test_doorprize_draw_selects_winner(): void
    {
        $this->seedDefaults();

        $user = User::factory()->create(['role' => 1, 'username' => 'admin']);
        $this->actingAs($user);

        Invitation::factory()->count(2)->create([
            'checkin_invitation' => now(),
        ]);

        $response = $this->post('/doorprize/draw');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}

