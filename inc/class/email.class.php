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

    /**
     * Create a new instance of email 
     */
    public function __construct() {
        $this->html = false;
    }

    /**
     * Set the recipient of the email
     */
    public function setTo($to) {
        $this->to = $to;
    }

    /**
     * Set the sender of the email
     */
    public function setFrom($from) {
        $this->from = $from;
    }

    /**
     * Set the email subject
     */
    public function setSubject($subject) {
        $this->subject = $subject;
    }

    /**
     * Set the plain text body of the email
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     * Set the html body of the email
     */
    public function setHtmlBody($body) {
        $this->htmlBody = $body;
        $this->html = true;
    }

    /**
     * Send the email
     */
    public function send() {
        if (MAIL_SERVER) {
            // Send via SMTP server
            // need to include https://github.com/PHPMailer/PHPMailer
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
            $headers = "From: <" . $this->from . ">\r\n";
            return mail($this->to,$this->subject,$this->body,$headers);
        }
    }
}