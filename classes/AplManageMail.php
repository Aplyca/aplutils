<?php
class AplManageMail
{
	
	function __construct() 
    {      	
 	
    }   
    
    static function sendMail($main_params, $transportData = null)
    {    	               
        $result = self::send($main_params['receiver'], $main_params['email_bcc_receivers'], $main_params['subject'], $main_params['body'], $transportData, $main_params );
        return $result;       
    }
    
    static function fetchMailTemplate($template, $set_tpl_variables, $get_tpl_variables = array(), $tpl = false)
    {
    	$email_template = array();
    	if (!is_object($tpl))
    	{
    		$tpl = eZTemplate::factory();
    	}
    	foreach ($set_tpl_variables as $var_name => $set_tpl_variable )
		{
			$tpl->setVariable( $var_name, $set_tpl_variable );
		}     	
		
		$email_template['body'] = $tpl->fetch( "design:" . $template );
		
		if ($email_template['body'] == '')
		{
			$template = "notification/default.tpl";
			$email_template['body'] = $tpl->fetch( "design:" . $template  );
		}
		
		$get_tpl_variables = array_merge( $get_tpl_variables, array('receiver', 'subject', 'email_cc_receivers', 'email_bcc_receivers', 'email_sender', 'email_reply_to'));
		
    	foreach ($get_tpl_variables as $get_tpl_variable )
		{
			$email_template[$get_tpl_variable] = $tpl->variable( $get_tpl_variable );
		}
		
        return $email_template;		
    } 
    
    static function send( $receiver, $addressList = false, $subject, $body, $transportData = null, $parameters = array() )
    {
        $ini = eZINI::instance();
        $mail = new eZMail();
        $receiver = self::prepareAddressString( $receiver, $mail );

		
        if ( $receiver == false )
        {
            eZDebug::writeError( 'Error with receiver', 'eZMailNotificationTransport::send()' );
            return false;
        }

        $notificationINI = eZINI::instance( 'notification.ini' );
        $emailSender = $notificationINI->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( 'MailSettings', 'EmailSender' );
        if ( !$emailSender )
            $emailSender = $ini->variable( "MailSettings", "AdminEmail" );

			
        $addressList_Bcc = self::prepareAddressString( $parameters['email_bcc_receivers'], $mail );		
        if ($addressList_Bcc AND is_array($addressList_Bcc))
        {    		
	        foreach ( $addressList_Bcc as $addressItem )
	        {
	            $mail->extractEmail( $addressItem, $email, $name );
	            $mail->addBcc( $email, $name );
	        }
        }
        
        
		$addressList_Cc = self::prepareAddressString( $parameters['email_cc_receivers'], $mail );				
        if ($addressList_Cc AND is_array($addressList_Cc))
        {    
	        foreach ( $addressList_Cc as $addressItem )
	        {
	            $mail->extractEmail( $addressItem, $email, $name );
	            $mail->addCc( $email, $name );
	        }
        }
      
        $mail->setReceiver( $receiver[0] );	
        $mail->setSender( $emailSender );
        $mail->setSubject( $subject );
        $mail->setBody( $body );
        
        if ( isset( $parameters['message_id'] ) )
            $mail->addExtraHeader( 'Message-ID', $parameters['message_id'] );
        if ( isset( $parameters['references'] ) )
            $mail->addExtraHeader( 'References', $parameters['references'] );
        if ( isset( $parameters['email_reply_to'] ) )
            $mail->addExtraHeader( 'In-Reply-To', $parameters['email_reply_to'] );
        if ( isset( $parameters['email_sender'] ) )
            $mail->setSenderText( $parameters['email_sender'] );

		if ( isset( $parameters['content_type'] ) )
		{
            $content_type = $parameters['content_type'];
        }
		else
		{
			$content_type = $ini->variable( 'MailSettings', 'ContentType' );
		}
		
		$mail->setContentType( $content_type );
        $mailResult = eZMailTransport::send( $mail );
        return $mailResult;
    }
    
    static function prepareAddressString( $addressList, $mail )
    {
        if ( is_array( $addressList ) )
        {
            $validatedAddressList = array();
            foreach ( $addressList as $address )
            {
                if ( $mail->validate( $address ) )
                {
                    $validatedAddressList[] = $address;
                }
            }
            return $validatedAddressList;
        }
        else if ( strlen( $addressList ) > 0 )
        {
            if ( $mail->validate( $addressList ) )
            {
                return array($addressList);
            }
        }
        return false;
    }   
    
     static function sendNotification( $notification, $params )
     {
     	$notifiation_ini = eZINI::instance('notification.ini');
     	
     	if (!(($notifiation_ini ->hasVariable("Notifications", "ActiveNotifications")) AND in_array($notification, $notifiation_ini ->variable("Notifications", "ActiveNotifications"))))
     	{
     		return false;
     	}
     
     	$template = 'notification/' . $notification . '.tpl';    	
     	if ($notifiation_ini ->hasVariable("Notification-" . $notification, 'Template'))
     	{
     		$template = $notifiation_ini ->variable("Notification-" . $notification, 'Template');
     	}

     	$email_params = AplManageMail::fetchMailTemplate($template, $params);
     	
        if ($notifiation_ini ->hasVariable("Notification-" . $notification, 'ContentType'))
     	{
     		$email_params['content_type'] = $notifiation_ini ->variable("Notification-" . $notification, 'ContentType');
     	}    
     	if ($notifiation_ini ->hasVariable("Notification-" . $notification, 'Receiver'))
     	{
     		$email_params['receiver'] = $notifiation_ini ->variable("Notification-" . $notification, 'Receiver');
     	}    	
     	if ($notifiation_ini ->hasVariable("Notification-" . $notification, 'Sender'))
     	{
     		$email_params['email_sender'] = $notifiation_ini ->variable("Notification-" . $notification, 'Sender');
     	}     	
    	if ($notifiation_ini ->hasVariable("Notification-" . $notification, 'BCCReceiver'))
     	{
     		$email_params['email_bcc_receivers'] = array_merge($notifiation_ini ->variable("Notification-" . $notification, 'BCCReceiver'), (array)$email_params['email_bcc_receivers']);
     	}
     	if ($notifiation_ini ->hasVariable("Notification-" . $notification, 'CCReceiver'))
     	{
     		$email_params['email_cc_receivers'] = array_merge($notifiation_ini ->variable("Notification-" . $notification, 'CCReceiver'), (array)$email_params['email_cc_receivers']);
     	}
        if ($notifiation_ini ->hasVariable("Notification-" . $notification, 'ReplyTo'))
     	{
     		$email_params['email_reply_to'] = $notifiation_ini ->variable("Notification-" . $notification, 'ReplyTo');
     	}     	
     	return AplManageMail::sendMail($email_params);
     }
    
}

?>