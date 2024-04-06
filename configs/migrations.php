<?php

declare(strict_types=1);

global $wpdb;

return [
	'table_storage'           => [
		'table_name'                 => 'hrhub_migration_versions',
		'version_column_name'        => 'version',
		'version_column_length'      => 191,
		'executed_at_column_name'    => 'executed_at',
		'execution_time_column_name' => 'execution_time',
	],

	'migrations_paths'        => [
		'HRHub\Migration' => __DIR__ . '/../src/Migration',
	],

	'all_or_nothing'          => true,
	'transactional'           => true,
	'check_database_platform' => true,
	'organize_migrations'     => 'none',
	'connection'              => null,
	'em'                      => null,
];
