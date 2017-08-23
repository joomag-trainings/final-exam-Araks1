<?php

namespace Service;
use PHPMailer;
use phpmailerException;

class MailService
{

    public static function sendMail($email,$accountVerifyHash)
    {


        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "tls";
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 587;
            $mail->Username = "letstalkforum0@gmail.com";
            $mail->Password = "letstalk1111";
            $mail->setFrom('letstalkforum0@gmail.com', 'Forum');
            $mail->addReplyTo('letstalkforum0@gmail.com', 'Forum');
            $mail->AddAddress($email, $email);
            $mail->Subject = 'Confirmation of account';
            $mail->msgHTML(" <p>Hi,</p>
        <p>            
        Thanks for Registration.  We have received a request for a creating account associated with this email address.
        </p>
        <p>
        To confirm , please click <a href='http://localhost/forum/public/index.php/login?hash=$accountVerifyHash'>here</a>.  If you did not initiate this request,
        please disregard this message .
        </p >
        <p >
        If you have any questions about this email, you may contact us at letstalkforum0@gmail.com .
        </p >
        <p >
                            With regards,
        <br >
                            The Forum . com Team
                            </p > ");

            $mail->send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}