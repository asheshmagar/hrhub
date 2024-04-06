<?php
/**
 * Connection class.
 */
namespace HRHub;

use Doctrine\DBAL\DriverManager;
use HRHub\Config;

/**
 * Connection class.
 */
class Connection {

	/**
	 * Connection.
	 *
	 * @var \Doctrine\DBAL\Connection|null
	 */
	private static ?\Doctrine\DBAL\Connection $conn = null;

	/**
	 * Constructor.
	 *
	 * @param Config $config
	 */
	public function __construct( private Config $config ) {
		self::$conn = DriverManager::getConnection( $config->db );
	}

	/**
	 * Get connection.
	 *
	 * @return \Doctrine\DBAL\Connection|null
	 */
	public static function get_connection() {
		return self::$conn;
	}
}
