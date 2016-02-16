<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public function __construct($array)
    {
        parent::__construct($array);

        $this->getCoordinates();

        if (mb_strlen($this->message) > self::INLINE_EMAIL_LENGTH) {
            $this->message_short = mb_strimwidth($this->message, 0, self::INLINE_EMAIL_LENGTH, '...', 'utf-8');
        }
    }

    public static function send($email, $subject, $message, $files, $place = NULL, $id_place = NULL, $additional = NULL)
    {
        foreach ($files as $file) {
            unset($file['email_uploaded_file_id']);
        }

        $Email = new self([
            'email' 	=> is_array($email) ? implode(",", $email) : $email,
            'subject' 	=> $subject,
            'message' 	=> nl2br($message),
            'files'		=> $files,
            'place'		=> $place,
            'id_place' 	=> $id_place,
            'additional'=> $additional,
        ]);

        $mail = self::initMailer();

        foreach ($files as $file) {
            $mail->addAttachment(self::UPLOAD_DIR . $file['name'], $file['uploaded_name']);
        }

        if (is_array($email)) {
            foreach ($email as $index => $e) {
                $mail->addBCC($e);
            }
        } else {
            $mail->addAddress($email);
        }
        $mail->Subject = $Email->subject;
        $mail->Body = $Email->message;

        $mail->send();

        $Email->save();
        return $Email;
    }

    public static function initMailer()
    {
        $mail = new PHPMailer;

        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.yandex.ru'; 					  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'info@ege-centr.ru';                // SMTP username
        $mail->Password = 'kochubey1981';                        // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to

        $mail->From = 'info@ege-centr.ru';
        $mail->FromName = 'ЕГЭ-Центр';

        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        return $mail;
    }
}
