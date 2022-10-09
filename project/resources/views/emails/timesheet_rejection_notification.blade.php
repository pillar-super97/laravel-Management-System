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
    The timesheet you have submitted for <?php echo $user->store; ?> on <?php echo $user->date; ?> has been rejected and 
    must be corrected and resent.  The reason for rejection is noted below: <br><br>
    <?php echo $user->comment; ?>.  <br><br><br>
</p>
</body>
</html>