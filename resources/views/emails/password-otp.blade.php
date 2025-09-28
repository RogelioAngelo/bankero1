<html>
<body>
    <p>Hello,</p>
    <p>Your password reset OTP is: <strong>{{ $otp }}</strong></p>
    <p>It will expire in 5 minutes.</p>
    @if(!empty($token))
        <p>If you can't use this device to enter the OTP, open this link on the device where you want to reset your password:</p>
        <p><a href="{{ url('/password/reset/from-otp/' . $token) }}">Reset password using this link</a></p>
    @endif
    <p>If you didn't request this, ignore this email.</p>
</body>
</html>
