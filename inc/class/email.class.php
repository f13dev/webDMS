<?php
// block direct access
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
  header("Location: ../../");
}
Class email {
    // Variables 
    private $to;
    private $from;
    private $subject;
    private $body;
    private $htmlBody;
    private $html;

    public function __construct() {
        $this->html = false;
    }

    public function setTo($to) {
        $this->to = $to;
    }

    public function setFrom($from) {
        $this->from = $from;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function setHtmlBody($body) {
        $this->htmlBody = $body;
        $this->html = true;
    }

    public function send() {
        if (MAIL_SERVER) {
            // Send via SMTP server
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host       = MAIL_HOST;
            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth   = true;
            $mail->Port       = MAIL_PORT;
            $mail->Username   = MAIL_USER;
            $mail->Password   = MAIL_PASS;
            $mail->isHTML(false);
            $mail->Subject = $this->subject;
            if ($this->html) {
                $email->body = $this->htmlBody;
            }
            $mail->AltBody = $this->body;
            $mail->send();
        } else {
            // Send via PHP mail
        }
    }
}