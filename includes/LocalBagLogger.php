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
 */

/**
 * PSR-3 logger that mimics the historic implementation of MediaWiki's former
 * wfErrorLog logging implementation.
 *
 * This logger is configured by the following global configuration variables:
 * - `$wgDebugLogFile`
 * - `$wgDebugLogGroups`
 * - `$wgDBerrorLog`
 * - `$wgDBerrorLogTZ`
 *
 * See documentation in DefaultSettings.php for detailed explanations of each
 * variable.
 *
 * @see \MediaWiki\Logger\LoggerFactory
 * @since 1.25
 * @copyright © 2014 Wikimedia Foundation and contributors
 */
class LocalBagLogger extends MediaWiki\Logger\LegacyLogger {
	protected static $msgs = [];

	public static function getMessages() {
		return self::$msgs;
	}

	/**
	 * Analyzes SELECT queries for potential inefficiencies.
	 *
	 * @param string|int $level
	 * @param string $message
	 * @param array $context
	 * @return null
	 */
	public function log( $level, $message, array $context = [] ) {
		if ( $this-> channel === 'DBQuery' && strpos( $message, 'SELECT' ) === 0 ) {
			global $wgLocalBagIgnoreNeedles;
			$ignore = false;
			if ( isset( $wgLocalBagIgnoreNeedles ) ) {
				foreach ( $wgLocalBagIgnoreNeedles as $needle ) {
					if ( strpos( $message, $needle ) !== false ) {
						$ignore = true;
					}
				}
			}

			if ( !$ignore ) {
				try {
					$dbr = wfGetDB( DB_REPLICA );
					$sql = 'EXPLAIN ' . $message;
					$extra = $dbr->fetchRow( $dbr->query( $sql, __METHOD__ ) )['Extra'];
					if ( strpos( $extra, 'filesort' ) != false ||
						(strpos( $extra, 'index' ) == false && strpos( $extra, 'where' ) != false) ) {
						self::$msgs[] = $extra . ': ' . $sql;
					}
				} catch ( Exception $e ) {
					self::$msgs[] = "Unable to analyze query $message";
				}
			}
		}
		return parent::log( $level, $message, $context );
	}
}
