<?php
/**
 * Table prefix subscriber.
 */

namespace HRHub\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

/**
 * TablePrefixSubscriber class.
 */
class TablePrefixSubscriber implements EventSubscriber {

	/**
	 * Prefix.
	 *
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->prefix = $wpdb->prefix;
	}

	/**
	 * Get subscribed events.
	 *
	 * @return array
	 */
	public function getSubscribedEvents(): array {
		return [ 'loadClassMetadata' ];
	}

	/**
	 * Load class metadata.
	 *
	 * @param LoadClassMetadataEventArgs $event_args
	 * @return void
	 */
	public function loadClassMetadata( LoadClassMetadataEventArgs $event_args ): void {
		$class_metadata = $event_args->getClassMetadata();
		$class_metadata->setTableName( $this->prefixTableName( $class_metadata ) );
	}

	/**
	 * Prefix table name.
	 *
	 * @param ClassMetadataInfo $class_metadata
	 * @return string
	 */
	private function prefixTableName( ClassMetadataInfo $class_metadata ): string {
		$table_name = $class_metadata->getTableName();

		if ( empty( $table_name ) ) {
			$naming_strategy = new DefaultNamingStrategy();
			$table_name      = $naming_strategy->classToTableName( $class_metadata->getName() );
		}

		return $this->prefix . $table_name;
	}
}
