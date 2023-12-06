<!DOCTYPE html>
<html>
<head></head>
<body>
<p>Hello,</p>
@if ($imageUrl)
    <img src="{{$imageUrl }} " alt="Image" height="100" width="100">
@endif
<p>{!! $emailContent !!} </p> <!-- Display the email content here -->
</body>
</html>
