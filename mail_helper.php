<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($user_email, $user_name, $otp_code) {
    $mail = new PHPMailer(true);

    try {
        // ⚙️ វគ្គកំណត់រចនាសម្ព័ន្ធក្បាលម៉ាស៊ីន SMTP របស់ Google
        $mail->isSMTP();
        $mail->Timeout = 15;
        $mail->SMTPConnectTimeout = 15;
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                 
        $mail->Username   = 'reaksakun93@gmail.com';              
        $mail->Password   = 'kxxl fjzl scnm qbrv';             
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       
        $mail->Port       = 587;                                  
        $mail->CharSet    = 'UTF-8';                              

        // 🔒 សំខាន់បំផុតសម្រាប់ Localhost XAMPP
        // វាបង្ខំឱ្យប្រព័ន្ធរំលងការឆែក SSL Certificate លើ Windows ទើបផ្ញើចេញទៅក្រៅលោកបាន
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // 👥 វគ្គកំណត់អ្នកផ្ញើ និងអ្នកទទួល
        $mail->setFrom('reaksakun93@gmail.com', 'Raksa Coffee Shop');
        $mail->addAddress($user_email, $user_name);               

        $mail->isHTML(true);
        $mail->Subject = 'លេខកូដផ្ទៀងផ្ទាត់គណនីបុគ្គលិក - Raksa Coffee Shop';
        
        $mail->Body    = "
            <div style='font-family: sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <h2 style='color: #2EBA7F; text-align: center;'>Raksa Coffee Shop</h2>
                <p>Hello <b>$user_name</b>,</p>
                <p>You have just requested to register as an employee in our POS system. Please use the following password to verify your real email:</p>
                <div style='background: #f7f7f7; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;'>
                    <span style='font-size: 28px; font-weight: bold; letter-spacing: 5px; color: #1B100A;'>$otp_code</span>
                </div>
                <p style='color: #888; font-size: 12px;'>* This code is only valid for 15 minutes. Please do not share this code with anyone else.</p>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p style='font-size: 11px; color: #aaa; text-align: center;'>Raksa Coffee Shop Management System © 2026</p>
            </div>
        ";

        $mail->send();
        return true; 
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false; 
    }
}
?>