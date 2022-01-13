<!DOCTYPE html>
<html>
<head>
    <title>Verification Code Email</title>
</head>

<body>
<h2>Welcome to the Our Site {{ $user->user_name }}</h2>
<br/>
<center>Here is the code for verification:</center>
<br/>
<br/>
<center><h1>{{ $user->code }}</h1></center>
<br>
<br>
<center>Please use this code to proceed.</center>
<br>
<br>
Thank you
</body>
</html>
