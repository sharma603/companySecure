@component('mail::message')
# Welcome to CompanySecure

Hello {{ $user->name }},

An account has been created for you on the CompanySecure platform.

Here are your login credentials:

**Email:** {{ $user->email }}  
**Password:** {{ $password }}

For security reasons, we recommend changing your password after your first login.

@component('mail::button', ['url' => route('login')])
Login Now
@endcomponent

Thank you,  
{{ config('app.name') }} Team
@endcomponent 