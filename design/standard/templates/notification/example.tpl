{set-block scope=root variable=subject}{"Subject email notification"|i18n('hapd/notification')}{/set-block}
{set-block scope=root variable=email_sender}myname@mycompany.com{/set-block}
{set-block scope=root variable=receiver}myname@mycompany.com{/set-block}
{set-block scope=root variable=email_cc_receivers}myname@mycompany.com{/set-block}
{set-block scope=root variable=email_bcc_receivers}myname@mycompany.com{/set-block}
{set-block scope=root variable=email_reply_to}noreply@mycompany.com{/set-block}

<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		{literal}
			<style type="text/css">
				h1 {color:#164375;font-family:Georgia,"Times New Roman",Times,serif;font-size:30px;font-weight:normal;}
			</style>
		{/literal}
	</head>
	<body> 
		<h1>Test</h1>
	</body>
</html> 