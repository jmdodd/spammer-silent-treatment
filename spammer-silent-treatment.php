<?php
/*
Plugin Name: Spammer Silent Treatment
Description: Remove known spammer email addresses from wp_mail()'s To, Cc, and Bcc fields. 
Version: 0.1
Author: Jennifer M. Dodd
Author URI: http://uncommoncontent.com/
*/

/*
	Copyright 2012 Jennifer M. Dodd <jmdodd@gmail.com>

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! class_exists( 'UCC_Spammer_Silent_Treatment' ) ) {
class UCC_Spammer_Silent_Treatment {
	public static $instance;

	public function __construct() {
		self::$instance = $this;
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function init() {
		add_filter( 'wp_mail', array( $this, 'wp_mail' ) );
	}

	// Source: wp-includes/pluggable.php
	// Derived from wp_mail().
	public function wp_mail( $args ) { 
		extract( $args );
	
		// Process headers for Cc and Bcc.
		$safe_headers = array();
		if ( ! empty( $headers ) ) {
			if ( ! is_array( $headers ) ) {
				$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			} else {
				$tempheaders = $headers;
			}
	
			// Process $tempheaders.
			if ( ! empty( $tempheaders ) ) {
				// Iterate through the raw headers.
				foreach ( (array) $tempheaders as $header ) {
					if ( strpos( $header, ':' ) === false ) {
						if ( false !== stripos( $header, 'boundary=' ) ) {
							$parts = preg_split( '/boundary=/i', trim( $header ) );
							$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
						}
						continue;
					}
					list( $name, $content ) = explode( ':', trim( $header ), 2 );
	
					// Cleanup surrounding whitespace. 
					$name	 = trim( $name	  );
					$content = trim( $content );
					
					// We're only interested in processing Cc and Bcc.
					switch ( strtolower( $name ) ) {
						case 'cc':
						case 'bcc':
							$recipients = (array) explode( ',', $content );
							$safe_content = $this->strip_spammers( $recipients );
							$safe_content = implode( ',', $safe_content );
							$safe_headers[] = implode( ':', array( $name, $safe_content ) );
							break;
						default:
							$safe_headers[] = implode( ':', array( $name, $content ) );
							break;
					}
				}
			}
		}
		// Leave as array for wp_mail().
		$headers = $safe_headers;
	
		// Process To addresses.
		$safe_to = array();
		if ( ! empty( $to ) ) {
			if ( ! is_array( $to ) )
				$to = explode( ',', $to );
			$safe_to = $this->strip_spammers( $to );
		}
		// Leave as array for wp_mail().
		$to = $safe_to;

		return compact( 'to', 'subject', 'message', 'headers', 'attachments' );
	}

	public function strip_spammers( $recipients = array() ) {
		$safe_recipients = array();
		foreach ( (array) $recipients as $recipient ) {
			$recipient_name = '';
			if( preg_match( '/(.*)<(.+)>/', $recipient, $matches ) ) {
				if ( count( $matches ) == 3 ) {
					$recipient_name = $matches[1];
					$recipient = $matches[2];
				}
			}
			$user = get_user_by( 'email', $recipient );
			if ( $user && $user->user_status == 1 ) {
				continue;
// Potential MultiSite code?
//			} elseif ( $user && function_exists( 'is_user_spammy' ) && is_user_spammy( $user->user_login ) ) {
//				continue;
			} else {
				if ( ! empty( $recipient_name ) )
					$safe_recipients[] = $recipient_name . ' <' . $recipient . '>';
				else
					$safe_recipients[] = $recipient;
			}
		}
		return $safe_recipients;
	}
} }	

new UCC_Spammer_Silent_Treatment;
