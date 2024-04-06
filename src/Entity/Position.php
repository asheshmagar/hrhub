<?php

namespace HRHub\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table( name: 'hrhub_positions' )]
class Position {

	#[Id]
	#[GeneratedValue]
	#[Column( type: 'bigint', options: [ 'unsigned' => true ] )]
	private $id;

	#[Column( type: 'string', length: 255 )]
	private $name;

	#[Column( type: 'text' )]
	private $description;

	#[OneToMany( targetEntity: Employee::class, mappedBy: 'position' )]
	private $employees;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->employees = new ArrayCollection();
	}

	/**
	 * Get id.
	 *
	 * @return integer|null
	 */
	public function get_id(): ?int {
		return $this->id;
	}

	/**
	 * Get name.
	 *
	 * @return string|null
	 */
	public function get_name(): ?string {
		return $this->name;
	}

	/**
	 * Set name.
	 *
	 * @param string $name
	 * @return self
	 */
	public function set_name( string $name ): self {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get employees.
	 *
	 * @return Collection|Employee[]
	 */
	public function get_employees(): Collection {
		return $this->employees;
	}

	public function add_employee( Employee $employee ): self {
		if ( ! $this->employees->contains( $employee ) ) {
			$this->employees[] = $employee;
			$employee->set_position( $this );
		}

		return $this;
	}

	/**
	 * Remove employee.
	 *
	 * @param Employee $employee
	 * @return self
	 */
	public function remove_employee( Employee $employee ): self {
		if ( $this->employees->removeElement( $employee ) ) {
			if ( $employee->get_position() === $this ) {
				$employee->set_position( null );
			}
		}

		return $this;
	}

	public function get_description(): ?string {
		return $this->description;
	}

	public function set_description( ?string $description ): self {
		$this->description = $description;

		return $this;
	}
}
