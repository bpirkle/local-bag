<?php

/**
 * Copyright © 2003-2008 Brion Vibber <brion@pobox.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This file is an extension to the MediaWiki software and cannot be used standalone\n";
	die( 1 );
}

$wgBdpBagExampleConfigVariable = true; // just a placeholder, for now

$wgAutoloadClasses['BdpBag'] = __DIR__ . '/BdpBag.class.php';

$wgExtensionFunctions[] = 'BdpBag::setup';
$wgHooks['BeforePageDisplay'][] = 'BdpBag::beforePageDisplay';
$wgHooks['AfterFinalPageOutput'][] = 'BdpBag::afterFinalPageOutput';

$wgMessagesDirs['BdpBag'] = __DIR__ . '/i18n';
