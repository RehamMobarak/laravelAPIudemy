Hello{{$user->name}}
You changed your email. Please confirm it using the link
{{route('verify',$user->verification_token)}}