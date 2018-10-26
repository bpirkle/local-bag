<?php

/**
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
 * @file
 * @ingroup Extensions
 */

/**
 * Class LocalBag
 *
 * Main class for the LocalBag MediaWiki extension.
 * This extension is miscellaneous local code that should never be pushed to the remote.
 */
class LocalBag {
	public static function setup() {
		if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
			register_shutdown_function( 'LocalBag::shutdownFn' );
		}
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
	 * Append custom output at the bottom of the page.
	 *
	 * Executes a file of the form T123456.php in the "local" directory under the mediawiki
	 * installation.  Fails gracefully if no such file exists.
	 *
	 * @param $output
	 * @return bool
	 */
	public static function afterFinalPageOutput( $output ) {
		// Capture the normal output.  This removes it from the output buffer.
		$out = ob_get_clean();

		// Append custom additions additions (normally debugging output, but can vary per task)
		ob_start();

		global $wgLocalTask;
		$localTask = $wgLocalTask ?? 'No local task specified';
		print '<hr>--- ' . $localTask . ' ---<hr><div style="margin: 10"><pre>';

		if ( class_exists( 'LocalBagLogger' ) ) {
			if ( count( LocalBagLogger::getMessages() ) > 0 ) {
				print '<h2>Dubious queries</h2>';
				print '<ul>';
				foreach ( LocalBagLogger::getMessages() as $msg ) {
					print "<li>$msg</li>";
				}
				print '</ul>';
			} else {
				print 'No dubious queries found<br />';
			}
		} else {
			print 'Dubious query detection not configured<br />';
		}

		$localTaskFile = __DIR__ . '/../../local/' . $localTask . '.php';
		if ( is_file( $localTaskFile ) ) {
			include_once $localTaskFile;
		} else {
			print "$localTaskFile not found";
		}
		print '</pre><div>';


		$out .= ob_get_clean();

		// Restore the normal output to the output buffer, with custom additions appended.
		ob_start();
		echo $out;

		return true;
	}

	/**
	 * Append debugging output at the end of a command line run
	 *
	 * Executes a file of the form T123456Cli.php in the "local" directory under the mediawiki
	 * installation.  Fails gracefully if no such file exists.
	 */
	public static function shutdownFn() {
		global $wgLocalTask;
		$localTask = $wgLocalTask ?? 'No local task specified';
		print '--- ' . $localTask . ' ---';

		if ( class_exists( 'LocalBagLogger' ) ) {
			if ( count( LocalBagLogger::getMessages() ) > 0 ) {
				print "Dubious queries\n";
				foreach ( LocalBagLogger::getMessages() as $msg ) {
					print "* $msg\n";
				}
			} else {
				print "No dubious queries found\n";
			}
		} else {
			print "Dubious query detection not configured\n";
		}

		// Should this hit a T123456Cli.php file instead, to allow give different CLI vs web?
		$localTaskFile = __DIR__ . '/../../local/' . $localTask . 'Cli.php';
		if ( is_file( $localTaskFile ) ) {
			include_once $localTaskFile;
		} else {
			print "$localTaskFile not found\n";
		}
	}
}
