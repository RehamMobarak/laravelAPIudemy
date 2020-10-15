{{-- Hello {{$user->name}}
Thanks for creating an account. Please verify using the link below:
{{route('verify',$user->verification_token)}} --}}
@component('mail::message')
Hello{{$user->name}}

Thanks for creating an account. Please verify using the link below:

@component('mail::button',['url'=>route('verify',$user->verification_token)])
    Click here to Verify your account
@endcomponent

@endcomponent