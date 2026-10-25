<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;

class AuthenticateFilamentPanel extends FilamentAuthenticate
{
    /**
     * Authenticate the user and gracefully redirect cross-portal attempts.
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();
        $panel = Filament::getCurrentPanel();

        if ($user instanceof FilamentUser && ! $user->canAccessPanel($panel)) {
            // Determine if the user is authorized for any other portal
            $staffPanel = Filament::getPanel('staff');
            $partnerPanel = Filament::getPanel('partner');
            $clientPanel = Filament::getPanel('client');

            $targetUrl = null;
            if ($user->canAccessPanel($staffPanel)) {
                $targetUrl = url($staffPanel->getPath());
            } elseif ($user->canAccessPanel($partnerPanel)) {
                $targetUrl = url($partnerPanel->getPath());
            } elseif ($user->canAccessPanel($clientPanel)) {
                $targetUrl = url($clientPanel->getPath());
            }

            if ($targetUrl && $targetUrl !== $request->url()) {
                abort(redirect($targetUrl));
            }

            abort(403, 'Unauthorized portal access.');
        }
    }
}
