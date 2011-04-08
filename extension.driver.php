<?php

	class Extension_Members_Openid extends Extension{

		public function about(){
			return array(
				'name' => 'Members with OpenID',
				'type' => 'Event',
				'version' => '0.2',
				'release-date' => '2011-04-06',
				'author' => array(
					'name' => 'Stephen Bau',
					'website' => 'http://www.domain7.com',
					'email' => 'stephen@domain7.com'),
				'description' => 'Integrate Members and OpenID Authentication extensions.'
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

			// Fetch the OpenID data
			$openid_data = $context['openid-data'];
			$email = $openid_data->sreg_data['email'];

			$em = Symphony::ExtensionManager(Frontend::instance());
			$ex = $em->create('members');
			
			// Fetch the member ID from the OpenID email address
			$id = extension_Members::$fields['email']->fetchMemberIDBy($email);
			
			if (is_array($id))
				$id = current($id);

			// Fetch the member entry data
			$entry = $ex->Member->fetchMemberFromID($id);
			
			if (!($entry instanceof Entry))
				return; // no member with that mail

			$credentials = $entry->getData();

			if (!$credentials) return;

			// Fetch the members section field IDs from the configuration file
			$fields = Symphony::Configuration()->get('members');
			
			$identity_field = $fields['identity'];
			$email_field = $fields['email'];
			$authentication_field = $fields['authentication'];

			$username = $credentials[$identity_field]['value'];
			$email = $credentials[$email_field]['value'];
			$password = $credentials[$authentication_field]['password'];

			// Populate an array with data to use for logging in the member
			$creds = array();
			$creds['username'] = $username;
			$creds['email'] = $email;
			$creds['password'] = $password;

			// Authenticate the member
			$ex->Member->login($creds, true);

			// Redirect on successful login using the $login_redirect setting in the configuration file
			$login_redirect = Symphony::Configuration()->get('login-redirect', 'openid-auth');
			if($login_redirect) {
				redirect(URL . $login_redirect);
			}

		}
	}