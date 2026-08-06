<?php
namespace App\Listeners;

use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

class AuthEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function handleUserLogin($event)
    {
        UserActivityLog::create([
            'user_id'    => $event->user->id,
            'activity'   => 'login_success',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout($event)
    {
        if ($event->user) {
            UserActivityLog::create([
                'user_id'    => $event->user->id,
                'activity'   => 'logout',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Handle failed login attempts.
     */
    public function handleUserLoginFailed($event)
    {
        $email = $event->credentials['email'] ?? 'unknown';
        $user = User::where('email', $email)->first();

        UserActivityLog::create([
            'user_id'    => $user ? $user->id : null,
            'activity'   => 'login_failed',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'details'    => 'Percobaan login gagal dengan email: ' . $email,
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class  => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
            Failed::class => 'handleUserLoginFailed',
        ];
    }
}
