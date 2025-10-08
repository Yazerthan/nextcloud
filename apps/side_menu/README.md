🤙 Nextcloud app / Custom menu 🎨
===============================

[![Build Status](https://ci.gitnet.fr/api/badges/deblan/side_menu/status.svg)](https://ci.gitnet.fr/deblan/side_menu)
[![Translations](https://translate.codeberg.org/widgets/custom-menu/-/application/svg-badge.svg)](https://translate.codeberg.org/engage/custom-menu/)
![Downloads](https://img.shields.io/badge/dynamic/json?color=brightgreen&label=downloads&query=%24.K_downloads&suffix=K&url=https%3A%2F%2Fapi-side-menu.deblan.org%2Fdownloads.php)

Allows you to modify the position of the main menu by creating a panel on the left of the interface or with a big menu on the top.
You can also add and sort custom categories, define apps that must be displayed in the top menu, etc. Fully customisable.

This application is rather suitable for instances that activate a lot of applications.

You can customize colors depending of the theme (Dark theme and Breeze Dark).

* [Installation and upgrade](#installation-and-upgrade)
* [How to contribute?](#how-to-contribute)
* [Support](#support)
* [Screenshots](https://gitnet.fr/deblan/side_menu/src/branch/master/screenshots/)

You like this app and you want to support me? ☕ [Buy me a coffee](https://www.buymeacoffee.com/deblan) or [Donate with liberapay](https://liberapay.com/deblan)

## [📘 Read the documentation](https://deblan.gitnet.page/side_menu_doc/)

Requirements
------------

* PHP >= 8.1

Installation and upgrade
------------------------

Custom menu is available from the app store.

```
$ cd /path/to/nextcloud
$ php occ app:install side_menu
```

If you want to install it from source, go to https://gitnet.fr/deblan/side_menu/releases and copy the link to the last release (side_menu_vX.Y.Z.tar.gz). Then:

```
$ cd /path/to/nextcloud/apps
$ VERSION=x.y.z; curl -sS https://gitnet.fr/deblan/side_menu/releases/download/v${VERSION}/side_menu_v${VERSION}.tar.gz | tar xvfz -
```

Administrators can edit many settings using the administration page.

Users can disable the menu using the page of personal settings.

Use the shortcut `Ctrl`+`o` to open and to hide the side menu. Use `tab` to navigate.

### Use first top menu app as default app

You can easily let Custom Menu redirect to the first app in the top menu by changing the following parameter in your `config/config.php`:

```
'defaultapp' => 'side_menu',
```

If the top menu is empty then it redirects to files.

How to contribute?
------------------

You can report a bug or request a feature by opening an issue: https://gitnet.fr/deblan/side_menu/issues

### You are a translator

Translations are managed from [translate.codeberg.org](https://translate.codeberg.org/projects/custom-menu/application/).

### You are a developer

* fork the repository
* install an instance of Nextcloud
* go to `apps/` and clone your repository
* go to `apps/side_menu` and run `make dep`

Build javascripts using `make build` (or `make watch` to build them in real time).

Then commit and create a pull request.

Support
-------

You can join the official room on Matrix: [#custommenu:neutralnetwork.org](https://matrix.to/#/#custommenu:neutralnetwork.org).


Notice
------

Because I believe in a free and decentralized Internet, [Gitnet](https://gitnet.fr) is **self-hosted at home**.
In case of downtime, you can download **Custom Menu** from [here](https://kim.deblan.fr/~side_menu/).
