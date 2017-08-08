<?php

error_reporting(E_ALL & ~E_NOTICE);

/* NOTES

Database:

id - int
firstname - text
lastname - text
email - text
password - text (md5)
autoresponder - text
deleted - int (0, 1)
datetime - time
ip_address - text
blah

*/

if ($_POST)
{
    echo "<pre>"; print_r($_POST); echo "</pre>";
    // echo "<pre>"; print_r($_SERVER); echo "</pre>";
    // do error checking

    foreach ($_POST as $k => $v)
    {
        $$k = trim(strip_tags($v));
    }

    $error = "no";

    // firstname empty
    if ($firstname == "")
    {
      $error = "yes";
      $error_message .= "First Name missing<br>\n";
    }

    // lastname empty
    if ($lastname == "")
    {
      $error = "yes";
      $error_message .= "Last Name missing<br>\n";
    }

    // email empty
    if ($email == "")
    {
      $error = "yes";
      $error_message .= "Email missing<br>\n";
    }

    // email invalid format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != "")
    {
      $error = "yes";
      $error_message .= "Email invalid<br>\n";
    }

    // email invalid domain
    $domain = substr(strrchr($email, "@"), 1);
    if (!checkdnsrr($domain, 'MX') && $email != "")
    {
      $error = "yes";
      $error_message .= "No MX record<br>\n";
    }

    // password empty
    if ($password == "")
    {
      $error = "yes";
      $error_message .= "Password missing<br>\n";
    }

    // autoresponder empty
    if ($autoresponder == "")
    {
      $error = "yes";
      $error_message .= "Autoresponder missing<br>\n";
    }

}

?>
<html>
    <head>
<?php echo $error_message; ?>
        <title>Register</title>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="container">
          <form method="post" action="">
            <div class="form-group row">
              <label for="firstname" class="col-sm-2 col-form-label">Firstname</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Firstname" value="<?php echo $firstname; ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="lastname" class="col-sm-2 col-form-label">Lastname</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Lastname" value="<?php echo $lastname; ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="email" class="col-sm-2 col-form-label">Email</label>
              <div class="col-sm-10">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo $email; ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="password" class="col-sm-2 col-form-label">Password</label>
              <div class="col-sm-10">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
              </div>
            </div>

            <div class="form-check-row">
            <div class="form-group row">
              <label for="autoresponder" class="col-sm-2 col-form-label">Autoresponder</label>
               <div class="col-sm-10">
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="autoresponder" id="autoresponder" value="activecampaign">
                    ActiveCampaign
                  </label>
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="autoresponder" id="autoresponder" value="infusionsoft">
                    Infusionsoft
                  </label>
                  <label class="form-check-label">
                    <input type="radio" class="form-check-input" name="autoresponder" id="autoresponder" value="ontraport">
                    Ontraport
                  </label>
               </div>
            </div>
            <div class="form-group row">
              <div class="offset-sm-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Sign Up</button>
              </div>
            </div>
          </form>
        </div>
    </body>
</html>
