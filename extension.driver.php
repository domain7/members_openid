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
			
			$id = extension_Members::$fields['email']->fetchMemberIDBy($email);
			
			// print_r($id); exit;

			if (is_array($id))
				$id = current($id);

			// print_r($id); exit;

			$entry = $ex->Member->fetchMemberFromID($id);
			
			// print_r($entry); exit;

			if (!($entry instanceof Entry))
				return; // no member with that mail

			$credentials = $entry->getData();

			if (!$credentials) return;

			// print_r($credentials); exit;

			$fields = Symphony::Configuration()->get('members');
			
			// print_r($fields); exit;

			$identity_field = $fields['identity'];
			$email_field = $fields['email'];
			$authentication_field = $fields['authentication'];

			$username = $credentials[$identity_field]['value'];
			$email = $credentials[$email_field]['value'];
			$password = $credentials[$authentication_field]['password'];

			$creds = array();
			$creds['username'] = $username;
			$creds['email'] = $email;
			$creds['password'] = $password;

			// print_r($creds); exit;

			$ex->Member->login($creds, true);

			$login_redirect = Symphony::Configuration()->get('login-redirect', 'openid-auth');
			if($login_redirect) {
				redirect(URL . $login_redirect);
			}

		}
	}