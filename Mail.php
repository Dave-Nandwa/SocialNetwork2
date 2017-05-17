<?php
require ('PHPMailer/PHPMailerAutoload.php');
class Mail {
        public static function sendMail($subject, $body, $address) {
                //Create a new PHPMailer instance
                $mail = new PHPMailer;
                //Tell PHPMailer to use SMTP
                $mail->isSMTP();
                //host
                $mail->Host = 'smtp.gmail.com';
                // use
                // $mail->Host = gethostbyname('smtp.gmail.com');
                // if your network does not support SMTP over IPv6
                //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
                $mail->Port = 587;
                //Set the encryption system to use - ssl (deprecated) or tls
                $mail->SMTPSecure = 'tls';
                //Whether to use SMTP authentication
                $mail->SMTPAuth = true;
                //$mail->SMTPDebug = 2;
                //$mail->Debugoutput = 'html';
                $mail->isHTML();
                $mail->Username = 'dnandwa5@gmail.com';
                $mail->Password = 'minutemaid';
                $mail->SetFrom('no-reply@howcode.org');
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->AddAddress($address);
                $mail->Send();
                if(!$mail->Send())
                {
                echo "Error sending: " . $mail->ErrorInfo;
                }
        }
}
?>