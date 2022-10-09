<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12pt; color: #777; font-weight: normal; line-height: 1.45;color:#5aaaba;">
Dear <strong style="text-transform: capitalize;"><?php echo $user->name; ?></strong>,&nbsp;
</p>

<p class="p1" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12pt; color: #777; font-weight: normal; line-height: 1.45;">
    During the pre inventory confirmation call, Store <?php echo $user->store; ?> on <?php echo $user->date; ?> 
    has informed us of the following: <br><br>
    <?php echo $user->comment; ?>.  <br><br><br>
    
    NOTE:  This is an automated message do not reply. If you have questions regarding this message please contact your supervisor.
</p>
</body>
</html>