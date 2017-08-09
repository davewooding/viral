<?php

error_reporting(E_ALL & ~E_NOTICE);

/* NOTES

http://localhost/test/register.php

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
//echo "<pre>"; print_r($_POST); echo "</pre>";
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

    if ($error == "no")
    {
        // include config / functions file
        if (file_exists("config-functions.php"))
        {
            include_once("config-functions.php");
        }
        else
        {
            die("missing config-functions.php file");
        }

        // prepare extra variables
        $password_md5 = md5($password);
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $blah = json_encode($_POST);

        // connect to database
        $db = connectDatabase($mysqli_host, $mysqli_username, $mysqli_password, $mysqli_db);

        // table
        $table = "members";

        // prepare variables for database, int values get nothing, text get backslashed
        $smart_firstname = quote_smart($firstname);
        $smart_lastname = quote_smart($lastname);
        $smart_email = quote_smart($email);
        $smart_password_md5 = quote_smart($password_md5);
        $smart_deleted = 0;
        $smart_autoresponder = quote_smart($autoresponder);
        $smart_ip_address = quote_smart($ip_address);
        $smart_blah = quote_smart($blah);

        //sql statement
        $sql = "SELECT * FROM $table WHERE email = $smart_email";
        $query = mysqli_query($db,$sql);
        $id = 0;
        if ($query)
        {
            if ($sql = mysqli_fetch_assoc($query))
            {
                do {
                    $id = stripslashes($sql['id']);
                }
                while ($sql = mysqli_fetch_assoc($query));
            }
        }

        //duplicate email search
        if (preg_match("/\+/", $email))
        {
          preg_match("/(.*?)\+.*?\@(.*)/", $email, $matches);
          $email_special = $matches[1]."%".$matches[2];
          $smart_email = quote_smart($email_special);
          $sql = "SELECT * FROM $table WHERE email LIKE $smart_email";
echo $sql."<br>";
          $query = mysqli_query($db,$sql);
          $id = 0;
          if ($query)
          {
              if ($sql = mysqli_fetch_assoc($query))
              {
                  do {
                      $id = stripslashes($sql['id']);
                  }
                  while ($sql = mysqli_fetch_assoc($query));
              }
          }
        }

        if ($id > 0)
        {
          $error = "yes";
          $error_message .= "Email already exists<br>\n";
        }

        if ($error == "no")
        {
            // sql statement
            $sql = "INSERT INTO $table SET firstname = $smart_firstname, lastname = $smart_lastname, email = $smart_email, password_md5 = $smart_password_md5,
            autoresponder = $smart_autoresponder, ip_address = $smart_ip_address, blah = $smart_blah ";

            // insert into database
            $query = mysqli_query($db,$sql);

            // error handling - write to error_log file if an error
            $error_mysqli = $message = $status = "";
            $error_mysqli = mysqli_error($db);
            if ($error_mysqli != "")
            {
                error_log("SQL: ".$sql." ERROR: ".$error_mysqli);
                $message = "Error adding";
                $status = "danger";
            }
            else
            {
                $message = "Successfully added\n";
                $status = "success";
                $confirm_url = "";
            }
        }
    }

}

$autoresponder_array = array(
    "activecampaign",
    "infusionsoft",
    "ontraport",
);

foreach ($autoresponder_array as $v)
{
    $V = ucfirst($v);
    $checked = "";
    if ($v == $autoresponder) $checked = "checked";
    $autoresponder_select .= "                  <label class=\"form-check-label\">\n";
    $autoresponder_select .= "                    <input type=\"radio\" class=\"form-check-input\" name=\"autoresponder\" id=\"autoresponder\" value=\"".$v."\" ".$checked.">\n";
    $autoresponder_select .= "                    ".$V."\n";
    $autoresponder_select .= "                  </label>\n";
}

?>
<html>
    <head>
<?php echo $message; ?>
<?php echo $error_message; ?>
        <title>Register</title>
        <?php if ($status == "success") {?>
        <!--<meta content="2;URL=<?php echo $confirm_url; ?>" http-equiv="refresh" />-->
        <?php } ?>
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
              <label for="firstname" class="col-sm-2 col-form-label">First Name</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name" value="<?php echo $firstname; ?>">
              </div>
            </div>
            <div class="form-group row">
              <label for="lastname" class="col-sm-2 col-form-label">Last Name</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Last Name" value="<?php echo $lastname; ?>">
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
<?php echo $autoresponder_select; ?>
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

<?php


/*

-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 09, 2017 at 02:03 AM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `viral`
--

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `firstname` text NOT NULL,
  `lastname` text NOT NULL,
  `email` text NOT NULL,
  `password_md5` text NOT NULL,
  `autoresponder` text NOT NULL,
  `deleted` int(11) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` text NOT NULL,
  `blah` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;
*/

 ?>
