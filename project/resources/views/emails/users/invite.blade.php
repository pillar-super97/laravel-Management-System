@component('mail::message')
# {{ config('app.name') }} invite you to access dashboard

Welcome {{$user->name}}

@component('mail::button', ['url' => route('accept_invitation', $user->crystal_token) ])
Accept Invitation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
