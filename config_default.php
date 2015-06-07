<?php
/**
 * This is an example configuration file for Conexting OS with the most commonly
 * required configurations. For more detailed configuration see the separate
 * configuration files in /protected/config.
 * 
 * Replace each occurrence of ######## with the appropriate configuration
 * and save the file as config.php.
 */
return array(
	/**
	 * Path to Yii Framework 
	 */
	'yiiPath'=>dirname(__FILE__).'/../../yii-1.1.15/framework',
	
	/**
	 * Name of the site (displayed in page titles, emails etc.)
	 */
	'name'=>'Conexting',
	
	/**
	 * Default display language
	 */
	'language'=>'fi',
	
	/**
	 * Available display languages
	 */
	'languages'=>array(
		'fi'=>'Suomi',
		'en'=>'English',
	),
	
	/**
	 * A list of controllers used in this site (used to handle url:s and
	 * listed as illegal names for a wall).
	 */
	'controllers'=>array('site','admin','client','sms','store'),
	
	/**
	 * Other illegal wall names (directories for static files etc.)
	 */
	'illegalWallNames'=>array('webmail','info','contact','js','images','less','themes'),
	
	/**
	 * Database connection settings
	 * See: http://www.yiiframework.com/doc/api/1.1/CDbConnection
	 */
	'db'=>array(
		'emulatePrepare' => true,
		'charset' => 'utf8',
		'connectionString' => 'mysql:host=########;dbname=########',
		'username' => '########',
		'password' => '########',
		'tablePrefix' => 'engine_'
	),
	
	/**
	 * SMTP server configuration for sending email
	 */
	'mail'=>array(
		'host' => '########',
		'username' => '########',
		'password' => '########',
		'port' => '465',
		'encryption'=> 'ssl',
	),
	
	/**
	 * Email address to use in 'from'-header
	 * (should match with the email address of the SMTP account)
	 */
	'fromEmail'=>'########',
	
	'twitter'=>array(
		/**
		* Message App ID for the application that sends and reads the messages
		* (when client has not logged in with his own account). This application
		* and the credentials must have read and write permission to the account.
		*/
		'messageApp'=>array(
			'id'=>0,
			'userId'=>'########',
			'name'=>'########',
			'token'=>'########',
			'tokenSecret'=>'########',
			'consumerKey'=>'########',
			'consumerSecret'=>'########',
		),
		/**
		* Login App ID for the application that is used when client connects
		* the account with his own Twitter account.
		*/
		'loginApp'=>array(
			'id'=>0,
			'userId'=>'########',
			'name'=>'########',
			'token'=>'########',
			'tokenSecret'=>'########',
			'consumerKey'=>'########',
			'consumerSecret'=>'########',
		),
	),
	
	/**
	 * The lifetime an unpublished wall will have when saved
	 * (lifetime is expanded each time the wall is saved)
	 */
	'unpublishedWallLifetime'=>'7 DAY',
	
	/**
	 * Number of days before the wall is going to be removed to send a
	 * notification to the client.
	 */
	'notifyBeforeWallDiesDays'=>3,
	/**
	 * Minimum wall lifetime (from created to dies) in days to send
	 * the notification before removal. If wall lifetime is less than this, no
	 * notification will be sent. (Note: when the wall is removed the
	 * notification is always sent.)
	 */
	'notifyBeforeWallDiesMinLifetime'=>14,
	/**
	 * Minimum wall using time (from published to expires) in days to send
	 * notification when the wall is expiring within the day.
	 */
	'notifyWhenWallExpiresMinUsetime'=>3,
	/**
	 * Number of days before a notification is sent about unpublished wall
	 * being removed. (Note: unpublished walls have no lifetime so above
	 * notifications will not be sent.)
	 */
	'notifyUnpublishedWallRemovedDays'=>3,
	
	/**
	 * Credit store parameters for Paytrail / Suomen Verkkomaksut interface.
	 * Setting to null disables the credit store.
	 * For test values, see http://www.verkkomaksut.fi/palvelut/palveluiden-kayttoonotto/maksujarjestelman-testaaminen/
	 */
	'store' => array(
		'merchantId'=>null,
		'merchantSecret'=>'########',
		'walls'=>array(
			'wall'=>array(
				'title'=>'Basic 3 month wall',
				'length'=>'1 month',
				'removedAfter'=>'3 month',
				'price'=>0,
			),
		),
		'licenses'=>array(
			'license'=>array(
				'title'=>'Basic 1 year license',
				'wallRemovedAfter'=>'3 month',
				'length'=>'1 year',
				'price'=>0,
			),
		),
		'sms'=>array(
			'sms100'=>array(
				'credit'=>100,
				'price'=>0,
			),
		),
		'vat'=>24, // VAT percent
	),
	
	/**
	 * Custom configuration for Yii config files
	 */
	'yiiconfig' => array(
		/**
		 * Common configuration to be used in both Web and Console
		 */
		'common'=>array(),
		
		/**
		 * Configuration for Site
		 */
		'site'=>array(),
		
		/**
		 * Configuration for Console
		 */
		'console'=>array(),
	),
);
