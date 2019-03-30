<?php
namespace flogert\utils;

define("SETTINGS", parse_ini_file("/home/justin/private/hekima.ini",true));
Class Config
{
	const DB_NAME="caseMgt";
	const DB_HOST=SETTINGS['database']['servername'];
	const DB_USER=SETTINGS['database']['username'];
	const DB_PASSWORD=SETTINGS['database']['password'];

	const EMAIL_HOST=SETTINGS['mail_config']['smtp_host'];
	const EMAIL_PORT=SETTINGS['mail_config']['smtp_port'];
	const EMAIL_USER=SETTINGS['mail_config']['smtp_username'];
	const EMAIL_PASSWORD=SETTINGS['mail_config']['smtp_password'];

	const SMS_USER=SETTINGS['africastalkin']['africastalking_username'];
	const SMS_KEY=SETTINGS['africastalkin']['africastalking_key'];

	const MPESA_ConsumerKey=SETTINGS['mpesa']['ConsumerKey'];
	const MPESA_ConsumerSecret=SETTINGS['mpesa']['ConsumerSecret'];
}