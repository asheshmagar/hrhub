<?php
/**
 * Config class.
 */
namespace HRHub;

/**
 * Config class.
 */
class Config {

	/**
	 * Config.
	 *
	 * @var array
	 */
	protected array $config = [
		'db'          => [
			'driver'   => 'pdo_mysql',
			'host'     => DB_HOST,
			'dbname'   => DB_NAME,
			'user'     => DB_USER,
			'password' => DB_PASSWORD,
		],
		'environment' => 'development',
	];

	/**
	 * Magic method get.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get( string $name ) {
		return $this->config[ $name ] ?? null;
	}
}
