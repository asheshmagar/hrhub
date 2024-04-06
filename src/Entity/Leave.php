<?php

namespace HRHub\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table( name: 'hrhub_leaves' )]
class Leave {

	#[Id]
	#[GeneratedValue( strategy: 'AUTO' )]
	#[Column( type: 'bigint', options: [ 'unsigned' => true ] )]
	private $id;

	#[Column( type: 'date' )]
	private $start_date;

	#[Column( type: 'date' )]
	private $end_date;

	#[Column( type: 'text' )]
	private $reason;

	#[Column( type: 'string', nullable: true, length: 255 )]
	private $status;

	#[ManyToOne( targetEntity: Employee::class, inversedBy: 'leaves' )]
	#[JoinColumn( name: 'employee_id', referencedColumnName: 'id' )]
	private $employee;

	/**
	 * Get id.
	 *
	 * @return integer|null
	 */
	public function get_id(): ?int {
		return $this->id;
	}

	/**
	 * Get start date.
	 *
	 * @return \DateTimeInterface|null
	 */
	public function get_start_date(): ?\DateTimeInterface {
		return $this->start_date;
	}

	/**
	 * Set start date.
	 *
	 * @param \DateTimeInterface $start_date
	 * @return self
	 */
	public function set_start_date( \DateTimeInterface $start_date ): self {
		$this->start_date = $start_date;

		return $this;
	}

	/**
	 * Get end date.
	 *
	 * @return \DateTimeInterface|null
	 */
	public function get_end_date(): ?\DateTimeInterface {
		return $this->end_date;
	}

	/**
	 * Set end date.
	 *
	 * @param \DateTimeInterface $end_date
	 * @return self
	 */
	public function set_end_date( \DateTimeInterface $end_date ): self {
		$this->end_date = $end_date;

		return $this;
	}

	/**
	 * Get reason.
	 *
	 * @return string|null
	 */
	public function get_reason(): ?string {
		return $this->reason;
	}

	/**
	 * Set reason.
	 *
	 * @param string $reason
	 * @return self
	 */
	public function set_reason( string $reason ): self {
		$this->reason = $reason;
		return $this;
	}

	/**
	 * Get status.
	 *
	 * @return string|null
	 */
	public function get_status(): ?string {
		return $this->status;
	}

	/**
	 * Set status.
	 *
	 * @param string|null $status
	 * @return self
	 */
	public function set_status( ?string $status ): self {
		$this->status = $status;

		return $this;
	}

	/**
	 * Get employee.
	 *
	 * @return Employee|null
	 */
	public function get_employee(): ?Employee {
		return $this->employee;
	}

	/**
	 * Set employee.
	 *
	 * @param Employee|null $employee
	 * @return self
	 */
	public function set_employee( ?Employee $employee ): self {
		$this->employee = $employee;

		return $this;
	}
}
