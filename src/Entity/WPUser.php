<?php

namespace HRHub\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table( name: 'users' )]
class WPUser {

	#[Id]
	#[Column( type: 'bigint', options: [ 'unsigned' => true ] )]
	#[GeneratedValue]
	protected $ID;

	#[Column( type: 'string', length: 255 )]
	protected $user_login;

	#[Column( type: 'string', length: 255 )]
	protected $user_pass;

	#[Column( type: 'string', length: 255 )]
	protected $user_nicename;

	#[Column( type: 'string', length: 255 )]
	protected $user_email;

	#[Column( type: 'string', length: 255 )]
	protected $user_url;

	#[Column( type: 'datetime' )]
	protected $user_registered;

	#[Column( type: 'string', length: 255 )]
	protected $user_activation_key;

	#[Column( type: 'integer' )]
	protected $user_status;

	#[Column( type: 'string', length: 250 )]
	protected $display_name;
}
