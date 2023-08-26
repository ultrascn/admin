
# UltraSCN\Admin

[![Build Status](https://github.com/ultrascn/admin/workflows/Build/badge.svg)](https://github.com/ultrascn/admin/actions)
[![Downloads this Month](https://img.shields.io/packagist/dm/ultrascn/admin.svg)](https://packagist.org/packages/ultrascn/admin)
[![Latest Stable Version](https://poser.pugx.org/ultrascn/admin/v/stable)](https://github.com/ultrascn/admin/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/ultrascn/admin/blob/master/license.md)

Administration extension for Nette.

<a href="https://www.janpecha.cz/donate/"><img src="https://buymecoffee.intm.org/img/donate-banner.v1.svg" alt="Donate" height="100"></a>


## Installation

[Download a latest package](https://github.com/ultrascn/admin/releases) or use [Composer](http://getcomposer.org/):

```
composer require ultrascn/admin
```

UltraSCN\Admin requires PHP 5.6.0 or later.


## Usage

**Copy assets from `assets` directory to your website public directory**

**Edit `config.neon`**

```neon
extensions:
	admin: UltraScn\Admin\DI\AdminExtension

admin:
	title: Admin
	homepagePresenter: 'Admin:Dashboard:default'
	signPresenter: 'Admin:Sign:in'
	signOutLink: 'Admin:Sign:out'
	assets:
		publicBasePath: '/'
		defaultEnvironment: 'production'
		scripts:
			- path/to/netteForms.js
			- ['path/to/less.js', 'development', 'critical']
		stylesheets:
			- ['path/to/ultrascn/admin/styles.css', 'production']
			- ['path/to/ultrascn/admin/styles.less', 'development']
		bundles:
			- nette/forms
	router:
		prefix: admin
		packages:
			dashboard: 'CmsDashboard:Dashboard:'
			orders: 'CmsOrders:Order:list'
			users: 'CmsUsers:User:'
		defaultPackage: dashboard
		appPresenter: 'MyApp:Admin:Invoice:'
```

**Create NavigationFactory for main menu and register it in config.neon**

```php
class NavigationFactory implements \UltraScn\Admin\INavigationFactory
{
	/**
	 * @param  int|string|NULL $userId
	 */
	public function create($userId)
	{
		$navigation = new \Inteve\Navigation\Navigation;
		$navigation->addPage('/', 'Dashboard', 'Admin:Dashboard:');
		$navigation->addPage('users', 'Users', 'Admin:User:');
		$navigation->addPage('users/roles', 'Roles', 'Admin:UserRole:');

		return $navigation;
	}
}
```

**Create SignFormFactory and register it in config.neon**

```php
class SignFormFactory implements UltraScn\Admin\Forms\ISignFormFactory
{
	// ...
}
```

Or use default `UltraScn\Admin\Forms\SignFormFactory`.

**Create implementation of Nette\Security\IAuthenticator and register it in config.neon**

Or use simple Nette implementation:

```
security:
	users:
		admin: password
```

**Create SignPresenter**

```php
class SignPresenter extends \UltraScn\Admin\Presenters\SignPresenter
{
}
```

**Create presenters for your admin interface**

```php
class DashboardPresenter extends \UltraScn\Admin\Presenters\SecuredPresenter
{
}

class UserPresenter extends \UltraScn\Admin\Presenters\SecuredPresenter
{
}
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
