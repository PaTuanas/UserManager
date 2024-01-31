<?php

if (!defined('_CODE')) {
    die('Access denied...');
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Change tilte header page
function layouts($layoutName='header', $data=[]) {
    if(file_exists(_WEB_PATH_TEMPLATES.'/layout/'.$layoutName.'.php')){
        require_once _WEB_PATH_TEMPLATES.'/layout/'.$layoutName.'.php';
    }
}

//Send email function
function sendMail($to, $subject, $content) {
    //Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'phanhtuan05@gmail.com';                     //SMTP username
    $mail->Password   = 'uoywgeopcqngucju';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('phanhtuan05@gmail.com', 'Admin');
    $mail->addAddress($to);     //Add a recipient
    

    //Content
    $mail -> CharSet = 'UTF-8';
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $content;

    $mail -> SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        )
    );

    $sendMail = $mail->send();
    // echo 'Message has been sent';

    if($sendMail) {
        return $sendMail;
    }

} catch (Exception $e) {
    error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
}
}

//Check Get method
function isGet() {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    }
    return false;
}

//Check Post method
function isPost() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

//Filter input 
function filter() {
    $filterArr = [];
    if (isGet()){
        if(!empty($_GET)) {
            foreach($_GET as $key => $value) {
                $key = strip_tags($key);
                if (is_array($value)) {
                    $filterArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                }
                else{
                    $filterArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }

    if (isPost()){
        if(!empty($_POST)) {
            foreach($_POST as $key => $value) {
                $key = strip_tags($key);
                if (is_array($value)) {
                    $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                }
                else{
                    $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }

    return $filterArr;
}

//Validate email address
function isEmail($email) {
    $checkEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
    return $checkEmail;
}

//Validate integer number
function isInteger($num) {
    $checkInt = filter_var($num, FILTER_VALIDATE_INT);
    return $checkInt;
}

//Validate float number
function isFloat($num) { 
    $checkFloat = filter_var($num, FILTER_VALIDATE_FLOAT);
    return $checkFloat;
}

//Phone number check
function isPhone($phone) { 
    $checkZero = false;
    if($phone[0] == '0') {
        $checkZero = true;
        $phone = substr($phone,1);
    }

    $checkNumber = false;
    if(isInteger($phone) && strlen($phone) == 9) {
        $checkNumber = true;
    }

    if ($checkNumber && $checkZero) {
        return true;
    }
    return false;
}

//Notification message
function getMsg($msg, $type ='success') {
    echo '<div class= "alert alert-'.$type.'">';
    echo $msg;
    echo '</div>';
}

//Redirect
function redirect($path = ''){
    header("Location: $path");
    exit;
}

function form_error($key, $errors, $beforeHtml = '', $afterHtml = '') {
    return (!empty($errors[$key])) ? $beforeHtml . reset($errors[$key]) . $afterHtml : null;
}

function old_data($key, $oldData, $default = null) { 
    return (!empty($oldData[$key])) ? ($oldData[$key]) : $default;
}

// Check state login 
function isLogin () {
    $checkLogin = false;
    if(getSession('loginToken')){
        $tokenLogin = getSession('loginToken');
        
        $queryToken = getRow("SELECT user_id FROM tokenlogin WHERE token = '$tokenLogin'");
        if (!empty($queryToken)) {
            $checkLogin = true;
        }
        else {
            removeSession('loginToken');
        }
    }
    return $checkLogin;
}