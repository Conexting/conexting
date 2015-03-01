<?php
/**
 * This is the default configuration file for Conexting OS parameters to adjust the
 * system functionality for different uses.
 */
return array(
	/**
	 * Administrator login credentials
	 * array('username' => 'password as sha1-hash')
	 */
	'admins'=>array(
		'admin' => sha1('########'),
	),
	
	/**
	 * Country options to select from in credit store and client details
	 */
	'countries'=>array(
		'fi'=>'Suomi',
	),
	
	/**
	 * Available wall themes
	 */
	'themes'=>array(
		'basic'=>'Basic',
	),
	
	/**
	 * Default SMS number (Conexting OS currently does not support multiple
	 * SMS numbers, so this is the SMS number for all walls with SMS)
	 */
	'defaultSmsNumber'=>'########',
	
	/**
	 * Available shared SMS keywords
	 * array('keyword' => 'label')
	 */
	'smsKeywords'=>array(
		'abc'=>'abc',
	),
	
	/**
	 * SMS-keywords for specific clients
	 * array('Client name'=>array('keyword1'=>'label','keyword2'=>'label'))
	 */
	'smsKeywordsForClients'=>array(
		/*
		'Client'=>array(
			'cn1'=>'cn1 (private)',
		)
		*/
	),
	
	/**
	 * Available video stream providers (each requires a provider embed code
	 * implemented in /protected/widgets/view/streamEmbed/providerKey.php)
	 */
	'streamProviders'=>array(
		'bambuser'=>'Bambuser',
		'ustream'=>'Ustream'
	),
	
	/**
	 * Font that can be used in a wall. Prepending font name with '_' indicates
	 * that the font is available in Google Web Fonts and the required CSS will
	 * be included in the wall if the font is selected.
	 */
	'fonts'=>array(
		'Arial'=>'Arial',
		'Arial Rounded MT'=>'Arial Rounded MT',
		'Chelsea Market'=>'_Chelsea Market',
		'Droid Serif'=>'_Droid Serif',
		'Helvetica Rounded'=>'Helvetica Rounded',
		'Ruluko'=>'_Ruluko',
		'Ruda'=>'_Ruda',
		'Times New Roman'=>'Times New Roman',
		'Trebuchet MS'=>'Trebuchet MS',
		'Magra'=>'_Magra',
		'Esteban'=>'_Esteban',
		'Jura'=>'_Jura',
	),
	
	/**
	 * Font sizes to select from (in pixels)
	 * array('size'=>'label')
	 */
	'fontSizes'=>array(
		8=>8,
		10=>10,
		12=>12,
		14=>14,
		16=>16,
		18=>18,
		20=>20,
		22=>22,
		24=>24,
		26=>26
	),
	
	/**
	 * Client login times out after the defined lifetime if the client
	 * has not logged in.
	 */
	'clientLoginLifetime'=>60*60*24*14, // 14 days
);
