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
#[Table( name: 'hrhub_reviews' )]
class Review {

	#[Id]
	#[GeneratedValue]
	#[Column( type: 'bigint', options: [ 'unsigned' => true ] )]
	private $id;

	#[Column( type: 'date' )]
	private $review_date;

	#[Column( type: 'text' )]
	private $comments;

	#[ManyToOne( targetEntity: Employee::class, inversedBy: 'reviews' )]
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
	 * Get review date.
	 *
	 * @return \DateTimeInterface|null
	 */
	public function get_review_date(): ?\DateTimeInterface {
		return $this->review_date;
	}

	/**
	 * Set review date.
	 *
	 * @param \DateTimeInterface $review_date
	 * @return self
	 */
	public function set_review_date( \DateTimeInterface $review_date ): self {
		$this->review_date = $review_date;

		return $this;
	}

	/**
	 * Get comments.
	 *
	 * @return string|null
	 */
	public function get_comments(): ?string {
		return $this->comments;
	}

	/**
	 * Set comments.
	 *
	 * @param string $comments
	 * @return self
	 */
	public function set_comments( string $comments ): self {
		$this->comments = $comments;

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
