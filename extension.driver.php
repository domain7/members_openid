<?php

	class Extension_Members_Openid extends Extension{

		public function about(){
			return array('name' => 'Members with OpenID',
						 'version' => '0.1',
						 'release-date' => '2011-03-13',
						 'author' => array('name' => 'Marco Sampellegrini',
										   'email' => 'm@rcosa.mp')
				 		);
		}
		
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/frontend/',
					'delegate' => 'openidAuthComplete',
					'callback' => 'authenticationComplete'
				)
			);
		}

		public function authenticationComplete($context)
		{
			if (!class_exists('extension_Members')) return;

			$openid_data = $context['openid-data'];
			$email = $openid_data->sreg_data['email'];

			$em = new ExtensionManager(Frontend::instance());
			$ex = $em->create('members');
			
			$id = 1;
			
			// print_r($ex->Member->findMemberIDFromIdentity($email)); exit;
			
			if (is_array($id))
				$id = current($id);

			$entry = $ex->Member->fetchMemberFromID($id);
			
			// print_r($entry); exit;

			if (!($entry instanceof Entry))
				return; // no member with that mail

			$field = 149;
			$credentials = $entry->getData($field);
			
			if (!$credentials) return;
			
			print_r($credentials); exit;

			$ex->Member->login($credentials['username'], $credentials['password'], $isHash = true);

			$login_redirect = Symphony::Configuration()->get('login-redirect', 'openid-auth');
			if($login_redirect) {
				redirect(URL . $login_redirect);
			}

		}
	}