<?php

require_once "../config.php";

// IN the htaccess you can set up the following
// SetEnv DEV_ENV development
// and it will always make this test so that you don't execute the mailchimp code
// since they do not offer a test server as of 2020-08-26
define('ENVIRONMENT', isset($_SERVER['DEV_ENV']) ? $_SERVER['DEV_ENV'] : 'production');
if(ENVIRONMENT == 'development'){
  $test = true;
}


// Check for empty fields
if(empty($_POST['name'])      ||
 empty($_POST['email'])     ||
 empty($_POST['organization'])     ||
 empty($_POST['message'])   ||
 !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))
{
 echo "No arguments Provided!";
 return false;
}

$name          = strip_tags(htmlspecialchars($_POST['name']));
$email_address = strip_tags(htmlspecialchars($_POST['email']));
$organization  = strip_tags(htmlspecialchars($_POST['organization']));
$message       = strip_tags(htmlspecialchars($_POST['message']));
$newsletter    = strip_tags(htmlspecialchars($_POST['newsletter']));
$signup        = "OGCAPI website contact form";
$mailchimpcode = "not set";

if($newsletter == 1){
  $data = array(
    'email'     => $email_address,
    'status'    => 'subscribed',
    'firstname' => strstr($name, " ", true),
    'lastname'  => strstr($name, " "),
    'orgname'   => $organization,
    'signup'    => $signup
  );
  $mailchimpcode = syncMailchimp($data, $test);
}


// Create the email and send the message
$to            = 'ogcapi@ogc.org'; // Add your email address inbetween the '' replacing yourname@yourdomain.com - This is where the form will send a message to.
$email_subject = "OGCAPI Contact Form:  $name";
$email_body    = "You have received a new message from the OGCAPI website contact form.\n\n"."Here are the details:\n\nName: $name\n\nEmail: $email_address\n\nNewsletter: $newsletter\n\nMailChimpCode:$mailchimpcode\n\nOrganization: $organization\n\nMessage:\n$message";
$headers       = "From: noreply@ogc.org\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
$headers       .= "Reply-To: $email_address";
mail($to,$email_subject,$email_body,$headers);
return true;


function syncMailchimp($data,$test) {
  $apiKey = _MAILCHIMP_API_KEY;
  $listId = _MAILCHIMP_LIST_ID;

  $memberId = md5(strtolower($data['email']));
  $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
  $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

  $json = json_encode([
    'email_address' => $data['email'],
    'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
    'merge_fields'  => [
      'FNAME'     => $data['firstname'],
      'LNAME'     => $data['lastname'],
      'ORGNAME'   => $data['orgname'],
      'SIGNUP'    => $data['signup'],
    ]
  ]);

  if($test == false){
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

    $result   = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
  }else {
    $httpCode = "123Test";
  }


  return $httpCode;
}

?>
