@component('mail::message')
# Confirmation Email

Please confirm you email address to get full access.

@component('mail::button', ['url' => url('/register/confirm?token='. $user->confirmation_token)])
Confirm now!
@endcomponent

Thanks,<br>
Sf's own personal forum
@endcomponent
