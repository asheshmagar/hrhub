<?php
/**
 * Employee new account email.
 *
 * This template can be overridden by copying it to yourtheme/hrhub/emails/employee-new-account.php.
 *
 * HOWEVER, on occasion HR Hub will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'hrhub:email:header' );
?>

<?php /* translators: %s: Employee username */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'hrhub' ), esc_html( $user->user_login ) ); ?></p>
<p><?php esc_html_e( 'Your new account has been created with the following details:.', 'hrhub' ); ?></p>
<p><?php esc_html_e( 'Username:', 'hrhub' ); ?> <strong><?php echo esc_html( $user->user_login ); ?></strong></p>
<p><?php esc_html_e( 'Password:', 'hrhub' ); ?> <strong><?php echo esc_html( $user->user_pass ); ?></strong></p>
<a href="<?php echo esc_url( $user->login_url ); ?>"><?php esc_html_e( 'Click here to login.', 'hrhub' ); ?></a>

<?php
do_action( 'hrhub:email:footer' );
