<?php

namespace HRHub\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity]
#[Table( name: 'hrhub_attendances' )]
class Attendance {

	#[Id]
	#[GeneratedValue]
	#[Column( type: 'bigint', options: [ 'unsigned' => true ] )]
	private $id;

	#[Column( type: 'time', nullable: true )]
	private $attendance_date;


	#[Column( type: 'time', nullable: true )]
	private $clock_in_time;

	#[Column( type: 'time', nullable: true )]
	private $clock_out_time;

	#[ManyToOne( targetEntity: 'Employee', inversedBy: 'attendances' )]
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
	 * Get attendance date.
	 *
	 * @return \DateTimeInterface|null
	 */
	public function get_attendance_date(): ?\DateTimeInterface {
		return $this->attendance_date;
	}

	/**
	 * Set attendance date.
	 *
	 * @param \DateTimeInterface $attendance_date
	 * @return self
	 */
	public function set_attendance_date( \DateTimeInterface $attendance_date ): self {
		$this->attendance_date = $attendance_date;

		return $this;
	}

	/**
	 * Get clock in time.
	 *
	 * @return \DateTimeInterface|null
	 */
	public function get_clock_in_time(): ?\DateTimeInterface {
		return $this->clock_in_time;
	}

	/**
	 * Set clock in time.
	 *
	 * @param \DateTimeInterface|null $clock_in_time
	 * @return self
	 */
	public function set_clock_in_time( ?\DateTimeInterface $clock_in_time ): self {
		$this->clock_in_time = $clock_in_time;
		return $this;
	}

	/**
	 * Get clock out time.
	 *
	 * @return \DateTimeInterface|null
	 */
	public function get_clock_out_time(): ?\DateTimeInterface {
		return $this->clock_out_time;
	}

	/**
	 * Set clock out time.
	 *
	 * @param \DateTimeInterface|null $clock_out_time
	 * @return self
	 */
	public function set_clock_out_time( ?\DateTimeInterface $clock_out_time ): self {
		$this->clock_out_time = $clock_out_time;
		return $this;
	}

	/**
	 * Get employees.
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
