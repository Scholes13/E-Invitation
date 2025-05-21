<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Mail Tracker event listeners
        'jdavidbakr\MailTracker\Events\EmailSentEvent' => [
            'App\Listeners\EmailSent',
        ],
        'jdavidbakr\MailTracker\Events\ViewEmailEvent' => [
            'App\Listeners\EmailViewed',
        ],
        'jdavidbakr\MailTracker\Events\LinkClickedEvent' => [
            'App\Listeners\EmailLinkClicked',
        ],
        'jdavidbakr\MailTracker\Events\EmailDeliveredEvent' => [
            'App\Listeners\EmailDelivered',
        ],
        'jdavidbakr\MailTracker\Events\ComplaintMessageEvent' => [
            'App\Listeners\EmailComplaint',
        ],
        'jdavidbakr\MailTracker\Events\PermanentBouncedMessageEvent' => [
            'App\Listeners\EmailBounced',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
