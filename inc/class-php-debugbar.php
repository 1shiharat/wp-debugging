<?php

use DebugBar\StandardDebugBar;


/**
 * Class WPLogger
 */
class PHPDebugBar {

	public $debugbar = null;

	static $instance = null;

	/**
	 * PHPDebugBar constructor.
	 * 初期化
	 */
	public function __construct() {

		if ( ! is_user_logged_in() ){
			return false;
		}
		
		$this->debugbar = new StandardDebugBar();

		$debugbarRenderer = $this->debugbar->getJavascriptRenderer()
		                                   ->setBaseUrl( plugins_url('../vendor/maximebf/debugbar/src/DebugBar/Resources', __FILE__  ) )
		                                   ->setEnableJqueryNoConflict( false );

		if ( defined( "SAVEQUERIES" ) ){
			global $wpdb;
			$collector = new WordpressDatabaseCollector( $wpdb );
			$this->debugbar->addCollector( $collector );
		}

		$this->debugbar->addCollector( new DebugBar\Bridge\MonologCollector( WPLogger::$logger ) );

		add_action( 'wp_head', function () use ( $debugbarRenderer ) {
			echo $debugbarRenderer->renderHead();
		} );

		add_action( 'wp_footer', function () use ( $debugbarRenderer ) {
			echo $debugbarRenderer->render();
		} );

	}

	public static function get_instance() {

		if ( static::$instance === null ) {
			static::$instance = new self();
		}

		return static::$instance;

	}

}

$debugbar = PHPDebugBar::get_instance();

