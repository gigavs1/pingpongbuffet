<!DOCTYPE html>
<html>

<head>

<script src="https://kit.fontawesome.com/de92694c6a.js" crossorigin="anonymous"></script>

</head>
</html>
<?php

          use PHPMailer\PHPMailer\PHPMailer;
          use PHPMailer\PHPMailer\Exception;

          if ($_SERVER["REQUEST_METHOD"] == "POST") {
              // Get form input values
              $name = $_POST["name"];
              $email = $_POST["email"];
              $subject = $_POST["subject"];
              $message = $_POST["message"];

              // Set up PHPMailer
              require_once "PHPMailer/src/Exception.php";
              require_once "PHPMailer/src/PHPMailer.php";
              require_once "PHPMailer/src/SMTP.php";

              $mail = new PHPMailer(true);

              try {
                  // SMTP configuration
                  $mail->isSMTP();
                  $mail->Host = 'smtp.gmail.com';
                  $mail->SMTPAuth = true;
                  $mail->Username = 't3st12356789@gmail.com'; // Replace with your Gmail address
                  $mail->Password = 'yasgjfpfvsljavki'; // Replace with your Gmail password
                  $mail->SMTPSecure = 'tls';
                  $mail->Port = 587;

                  // Sender and recipient
                  $mail->setFrom($email, $name);
                  $mail->addAddress('t3st12356789@gmail.com'); // Replace with your admin email address

                  // Email content
                  $mail->isHTML(true);
                  $mail->Subject = 'Feedback from ' . $name;
                  $mail->Body = '<p><strong>Name:</strong> ' . $name . '</p><p><strong>Email:</strong> ' . $email . '</p><p><strong>Subject:</strong> ' . $subject . '</p><p><strong>Message:</strong> ' . $message . '</p>';

                  // Send email
                  $mail->send();

                  // Clear input fields
                  $name = '';
                  $email = '';
                  $message = '';

                  echo '<p style="background-color:#df1529;position:relative;bottom:30px;text-align:center;">Thank you for your feedback! We will get back to you as soon as possible.</p>
                  <div style="text-align:center"><i class="fa-solid fa-envelope-circle-check fa-3x fa-shake" style="margin:0 auto;"></i></div>';
              } catch (Exception $e) {
                  echo '<p class="error">Message could not be sent. Mailer Error: ' . $mail->ErrorInfo . '</p>';
              }
          }

        ?>