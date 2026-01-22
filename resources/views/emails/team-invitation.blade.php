@component('mail::message')
<div style="font-family: 'Helvetica Neue', Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p style="margin: 0 0 16px;">{{ __('You have been invited to join the :team team!', ['team' => $invitation->team->name]) }}</p>

    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
        <p style="margin: 0 0 16px;">{{ __('If you do not have an account, you may create one by clicking the button below. After creating an account, you may click the invitation acceptance button in this email to accept the team invitation:') }}</p>

        @component('mail::button', ['url' => route('login')])
            {{ __('Access JCT Agency Suite') }}
        @endcomponent

        <p style="margin: 16px 0;">{{ __('If you already have an account, you may accept this invitation by clicking the button below:') }}</p>

    @else
        <p style="margin: 0 0 16px;">{{ __('Access to the JCT Agency suite is managed internally. If you require credentials, please contact your JCT administrator. Once your account is active you can accept this invitation using the button below:') }}</p>
    @endif


    @component('mail::button', ['url' => $acceptUrl])
        {{ __('Accept Invitation') }}
    @endcomponent

    <p style="margin: 16px 0 0; color: #6b7280;">{{ __('If you did not expect to receive an invitation to this team, you may discard this email.') }}</p>
</div>
@endcomponent
