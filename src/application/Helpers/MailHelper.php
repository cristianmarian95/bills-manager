<?php

namespace App\Helpers;

class MailHelper extends \App\App
{
    public function recover($email, $key){
        $subject = "Recover account";
        $message = "
        <html>
            <head>
                <title>Recover account</title>
            </head>
            <body>
                <p>Click on the link below to change your password!</p>
                <p><a href='". $this->request->getUri()->getBaseUrl() . $this->router->pathFor('changePassword') . $key . "'>". $this->request->getUri()->getBaseUrl() . $this->router->pathFor('changePassword') . $key . "</a></p>
                <p>If your did not request the account recover please ignore this message.</p>
            </body>
        </html>
        ";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: <webmaster@example.com>' . "\r\n";
        $headers .= 'Cc: myboss@example.com' . "\r\n";
        return mail($email,$subject,$message,$headers);
    }
}