<?php
/**
 * Employee entity.
 */

namespace HRHub\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table( name: 'hrhub_employees' )]
class Employee {

	#[Id]
	#[GeneratedValue( strategy: 'AUTO' )]
	#[Column( type: 'bigint', options: [ 'unsigned' => true ] )]
	private $id;

	#[Column( type: 'string' )]
	private $name;

	#[Column( type: 'string', length: 255 )]
	private $email;

	#[Column( type: 'string', length: 20 )]
	private $phone_number;

	#[Column( type: 'date' )]
	private $date_of_employment;

	#[Column( type: 'date' )]
	private $date_of_birth;

	#[Column( type: 'string', length: 255 )]
	private $address;

	#[Column( type: 'string', nullable: true )]
	private $documents;

	#[ManyToOne( targetEntity: Department::class, inversedBy: 'employees' )]
	#[JoinColumn( name: 'department_id', referencedColumnName: 'id' )]
	private $department;

	#[ManyToOne( targetEntity: Position::class )]
	#[JoinColumn( name: 'position_id', referencedColumnName: 'id' )]
	private $position;

	#[OneToMany( targetEntity: Leave::class, mappedBy: 'employee' )]
	private $leaves;

	#[OneToMany( targetEntity: Review::class, mappedBy: 'employee' )]
	private $reviews;

	#[OneToMany( targetEntity: Attendance::class, mappedBy: 'employee', orphanRemoval: true )]
	private $attendances;

	#[Column( type: 'string' )]
	private $status;

	#[Column( type: 'float', nullable: true )]
	private $salary;

	#[OneToOne( targetEntity: WPUser::class, cascade: [ 'persist', 'remove' ] )]
	#[JoinColumn( name: 'wp_user_id', referencedColumnName:'ID' )]
	private $wp_user_id;

	#[Column( type: 'string', length: 255 )]
	private $employment_type;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->reviews     = new ArrayCollection();
		$this->leaves      = new ArrayCollection();
		$this->attendances = new ArrayCollection();
	}

	public function get_wp_user_id(): ?WPUser {
		return $this->wp_user_id;
	}

	public function set_wp_user_id( ?WPUser $wp_user_id ): self {
		$this->wp_user_id = $wp_user_id;
		return $this;
	}

	/**
	 * Get reviews.
	 *
	 * @return Collection
	 */
	public function get_reviews(): Collection {
		return $this->reviews;
	}

	/**
	 * Add review.
	 *
	 * @param Review $review
	 * @return self
	 */
	public function add_review( Review $review ): self {
		if ( $this->reviews->contains( $review ) ) {
			return $this;
		}
		$this->reviews->add( $review );
		return $this;
	}

	/**
	 * Remove review.
	 *
	 * @param Review $review
	 * @return self
	 */
	public function remove_review( Review $review ): self {
		if ( $this->reviews->removeElement( $review ) ) {
			if ( $review->get_employee() === $this ) {
				$review->set_employee( null );
			}
		}
		return $this;
	}

	/**
	 * Get leaves.
	 *
	 * @return Collection
	 */
	public function get_leaves(): Collection {
		return $this->leaves;
	}

	/**
	 * Add leave.
	 *
	 * @param Leave $leave
	 * @return self
	 */
	public function add_leave( Leave $leave ): self {
		if ( $this->leaves->contains( $leave ) ) {
			return $this;
		}
		$this->leaves->add( $leave );
		return $this;
	}

	/**
	 * Remove leave.
	 *
	 * @param Leave $leave
	 * @return self
	 */
	public function remove_leave( Leave $leave ): self {
		if ( $this->leaves->removeElement( $leave ) ) {
			if ( $leave->get_employee() === $this ) {
				$leave->set_employee( null );
			}
		}
		return $this;
	}

	/**
	 * Get attendances.
	 *
	 * @return Collection
	 */
	public function get_attendances(): Collection {
		return $this->attendances;
	}

	/**
	 * Add attendance.
	 *
	 * @param Attendance $attendance
	 * @return self
	 */
	public function add_attendance( Attendance $attendance ): self {
		if ( $this->attendances->contains( $attendance ) ) {
			return $this;
		}
		$this->attendances->add( $attendance );
		return $this;
	}

	/**
	 * Remove attendance.
	 *
	 * @param Attendance $attendance
	 * @return self
	 */
	public function remove_attendance( Attendance $attendance ): self {
		if ( $this->attendances->removeElement( $attendance ) ) {
			if ( $attendance->get_employee() === $this ) {
				$attendance->set_employee( null );
			}
		}
		return $this;
	}

	/**
	 * Get email.
	 *
	 * @return string|null
	 */
	public function get_email(): ?string {
		return $this->email;
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
	 * Set id.
	 *
	 * @param integer $id
	 * @return self
	 */
	public function set_id( int $id ): self {
		$this->id = $id;
		return $this;
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
	 * Set email.
	 *
	 * @param string $email
	 * @return self
	 */
	public function set_email( string $email ): self {
		$this->email = $email;
		return $this;
	}

	/**
	 * Get phone number.
	 *
	 * @return string|null
	 */
	public function get_phone_number(): ?string {
		return $this->phone_number;
	}

	/**
	 * Set phone number.
	 *
	 * @param string $phone_number
	 * @return self
	 */
	public function set_phone_number( string $phone_number ): self {
		$this->phone_number = $phone_number;
		return $this;
	}

	/**
	 * Get hire date.
	 *
	 * @return DateTime|null
	 */
	public function get_date_of_employment(): ?DateTime {
		return $this->date_of_employment;
	}

	/**
	 * Set hire date.
	 *
	 * @param DateTime $date_of_employment
	 * @return self
	 */
	public function set_date_of_employment( DateTime $date_of_employment ): self {
		$this->date_of_employment = $date_of_employment;
		return $this;
	}

	/**
	 * Get birth date.
	 *
	 * @return DateTime|null
	 */
	public function get_date_of_birth(): ?DateTime {
		return $this->date_of_birth;
	}

	/**
	 * Set birth date.
	 *
	 * @param DateTime $date_of_birth
	 * @return self
	 */
	public function set_date_of_birth( DateTime $date_of_birth ): self {
		$this->date_of_birth = $date_of_birth;
		return $this;
	}

	/**
	 * Get department.
	 *
	 * @return Department|null
	 */
	public function get_department(): ?Department {
		return $this->department;
	}

	/**
	 * Set department.
	 *
	 * @param Department|null $department
	 * @return self
	 */
	public function set_department( ?Department $department ): self {
		$this->department = $department;
		return $this;
	}

	/**
	 * Get position.
	 *
	 * @return Position|null
	 */
	public function get_position(): ?Position {
		return $this->position;
	}

	/**
	 * Set position.
	 *
	 * @param Position|null $position
	 * @return self
	 */
	public function set_position( ?Position $position ): self {
		$this->position = $position;
		return $this;
	}

	public function get_address(): ?string {
		return $this->address;
	}

	public function set_address( string $address ): self {
		$this->address = $address;
		return $this;
	}

	public function get_documents() {
		$docs = $this->documents;
		$docs = maybe_unserialize( $docs );
		if ( ! is_array( $docs ) || empty( $docs ) ) {
			return [];
		}
		return array_filter(
			array_map(
				function ( $doc ) {
					$attachment = get_attached_media( 'image', $doc );
					if ( $attachment ) {
						return $attachment;
					}
					return false;
				},
				$docs
			)
		);
	}

	public function set_documents( $documents ) {
		$this->documents = maybe_serialize( $documents );
		return $this;
	}

	public function get_status(): ?string {
		return $this->status;
	}

	public function set_status( string $status ) {
		$this->status = $status;
		return $this;
	}

	public function get_salary(): ?float {
		return $this->salary;
	}

	public function set_salary( float $salary ) {
		$this->salary = $salary;
		return $this;
	}

	public function get_employment_type(): ?string {
		return $this->employment_type;
	}

	public function set_employment_type( ?string $employment_type ) {
		$this->employment_type = $employment_type;
		return $this;
	}
}
