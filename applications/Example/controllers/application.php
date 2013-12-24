<?php
	abstract class application {
		/**
		 * @route /
		 */
		public static function main() {
			echo pow(3, 686);
			/*MySQL::init();
			if($session = session::pull()) {
				print_r($session);
			} else {
				print_r("not authed");
			}*/
		}

		/**
		 * @route /gateway
		 */
		public static function gateway() {
			echo "gateway accessed";
			print_r($_REQUEST);
			file_put_contents(APP_CACHE."/dumped.txt", var_export($_REQUEST, true));
		}

		/**
		 * This is my cool action
		 * @route /bind/([a-zA-Z0-9_]+)
		 */
		public static function bind($login) {
			MySQL::init();
			$session = session::push($login);
			session::bind($session);
		}
	}