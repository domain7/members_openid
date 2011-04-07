# Members with OpenID #

This is a Symphony CMS extension that integrates the OpenID Authentication extension with the Members extension.

- Authors: Marco Sampellegrini ([alpacaaa](http://github.com/alpacaaa/)) and Stephen Bau ([domain7](http://github.com/domain7/))
- Github repository: http://github.com/domain7/members_openid/
- Release date: 6th April 2011
- Version: 0.2


## Installation

Enable the extension [as always](http://symphony-cms.com/learn/tasks/view/install-an-extension/).
This extension assumes that you have already installed the [Members extension](http://github.com/symphonycms/members) and the [OpenID Authentication extension]((http://github.com/alpacaaa/openid_auth)).


## Basic Usage

After a succesful authentication, a delegate is fired: `openidAuthComplete`.
This extension modifies the callback to associate the OpenID data with a member ID
using the email address to match the member with the OpenID. The member is authenticated,
and the extension redirects to the URL set in the configuration file. The `login-redirect`
preference can be set in the `openid-auth` array:

	###### OPENID-AUTH ######
	'openid-auth' => array(
		'store-path' => '/path/to/store/openid',
		'sreg-required-fields' => null,
		'sreg-optional-fields' => null,
		'login-redirect' => '/dashboard/',
		'logout-redirect' => '/login/',
	),
	########

