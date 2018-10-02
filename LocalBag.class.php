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
 *
 * Main class for the LocalBag MediaWiki extension.
 * This extension is miscellaneous local code that should never be pushed to the remote.
 *
 * @file
 * @ingroup Extensions
 */
class LocalBag {
	public static function setup() {
		return true;
	}

	/**
	 * We could add scripts, styles, and other header elements here if need be
	 *
	 * @param OutputPage $out
	 * @param Skin $skin
	 * @return bool
	 */
	public static function beforePageDisplay( OutputPage &$out, Skin &$skin ) {
		return true;
	}

	/**
	 * Append custom output at the bottom of the page
	 *
	 * @param $output
	 * @return bool
	 */
	public static function afterFinalPageOutput( $output ) {
		// Capture the normal output.  This removes it from the output buffer.
		$out = ob_get_clean();

		// Append custom additions additions (normally debugging output, but can vary per task)
		ob_start();
		require_once '/vagrant/mediawiki/local/local.php';
		$out .= ob_get_clean();

		// Restore the normal output to the output buffer, with custom additions appended.
		ob_start();
		echo $out;

		return true;
	}
}
