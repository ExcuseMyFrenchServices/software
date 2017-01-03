<div>
    <img src="<?php echo $message->embed('http://staff.excusemyfrenchservices.com/img/logo_small.png'); ?>">
    <h1>Welcome to Excuse my French Services!</h1>

    <h3>Your login information is:</h3>
    <p><b>Username:</b> {{ $user['username'] }}</p>
    <p><b>Password:</b> emf2015 </p>

    <h4>To change the password, please click the link below:</h4>
    <p>http://staff.excusemyfrenchservices.com/reset/{{ $hash }}</p>
</div>
