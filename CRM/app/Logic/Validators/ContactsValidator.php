<?php
	
	namespace App\Logic\Validators;
	
	class ContactsValidator extends Validators {
		/**
		 * Validates emails
		 *
		 * @param $string
		 * @return bool
		 */
		public static function email($string) {
			if (strpos($string, '@') === false) {
				return false;
			} else {
				return $string;
			}
		}
		
		/**
		 * Validates phones
		 *
		 * @param $string
		 * @return bool
		 */
		public static function phone($string) {
			$string = preg_replace('/[^0-9]/', '', $string);
			if (mb_strlen($string) < 9) {
				return false;
			} else {
				return $string;
			}
		}
		
		/**
		 * Validates facebook adresses
		 *
		 * @param $string
		 * @return bool
		 */
		public static function facebook($string) {
			if (strpos($string, 'facebook.com/') === false) {
				return false;
			} else {
				return $string;
			}
		}
	}