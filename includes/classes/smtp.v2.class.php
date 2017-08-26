<?php

class SMTPClient
{

    function SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $headers, $body)
    {
        $this->SmtpServer = $SmtpServer;
        $this->SmtpUser = base64_encode ($SmtpUser);
        $this->SmtpPass = base64_encode ($SmtpPass);
        $this->from = $from;
        $this->to = $to;
        $this->headers = $headers;

        $this->subject = $subject;
        $this->body = $body;
	
        if ($SmtpPort == "") {
            $this->PortSMTP = 25;
        } else {
            $this->PortSMTP = $SmtpPort;
        }
    }

    function SendMail ()
    {
        if ($SMTPIN = fsockopen ($this->SmtpServer, $this->PortSMTP)) {           
            fputs ($SMTPIN, "EHLO ".$HTTP_HOST."\r\n");  
            $talk["hello"] = fgets ( $SMTPIN, 1024 ); 
                   
            fputs($SMTPIN, "auth login\r\n");
            $talk["res"]=fgets($SMTPIN,1024);
			fputs($SMTPIN, $this->SmtpUser."\r\n");
            $talk["user"]=fgets($SMTPIN,1024);
		    
		    fputs($SMTPIN, $this->SmtpPass."\r\n");
			$talk["pass"]=fgets($SMTPIN,256);
			   	    
            fputs ($SMTPIN, "MAIL FROM: <".$this->from.">\r\n");  
            $talk["From"] = fgets ( $SMTPIN, 1024 );  
            
            fputs ($SMTPIN, "RCPT TO: <".$this->to.">\r\n");  
            $talk["To"] = fgets ($SMTPIN, 1024); 
           
            fputs($SMTPIN, "DATA\r\n");
			$talk["data"]=fgets( $SMTPIN,1024 );
           
			//fputs($smtpConnect, "To: $to\nFrom: $from\nSubject: $subject\n$headers\n\n$message\n.\n");
			//$smtpResponse = fgets($smtpConnect, 515);
			//$logArray['data2response'] = "$smtpResponse";
			
			fputs($SMTPIN, "Subject: ".$this->subject."\r\n".$this->headers." \r\n\r\n\r\n".$this->body."\r\n.\r\n");
			$talk["send"]=fgets($SMTPIN,256);
           
            //CLOSE CONNECTION AND EXIT ... 
		   
            fputs ($SMTPIN, "QUIT\r\n");  
            fclose($SMTPIN);   
        }  
        return $talk;
    }
}