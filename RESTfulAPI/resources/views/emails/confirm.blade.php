{{-- Hello{{$user->name}}
You changed your email. Please confirm it using the link
{{route('verify',$user->verification_token)}} --}}

@component('mail::message')
Hello{{$user->name}}
You changed your email. Please confirm it using the button

@component('mail::button',['url'=>route('verify',$user->verification_token)])
    Click here to confirm your new mail
@endcomponent

@endcomponent