Hello {{$user->name}}
Thanks for creating an account. Please verify using the link below:
{{route('verify',$user->verification_token)}}