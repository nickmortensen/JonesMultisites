<?php
/**
 * The `wp_rig()` function.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/**
 * Provides access to all available template tags of the theme.
 *
 * When called for the first time, the function will initialize the theme.
 *
 * @return Template_Tags Template tags instance exposing template tag methods.
 */
function wp_rig() : Template_Tags {
	static $theme = null;

	if ( null === $theme ) {
		$theme = new Theme();
		$theme->initialize();
	}

	return $theme->template_tags();
}

/**
 * Wrap intput in '<pre>' tags and print_r.
 *
 * @param mix $input Anything you want printed and wrapped in a pre tag.
 */
function wrap( $input ) {
	echo '<pre>';
	var_dump( $input ); //phpcs:ignore
	echo '</pre>';
}

/**
 * Dump the debug information.
 *
 * @param string $input Standard Input.
 * @param bool   $collapse Standard Input.
 */
function dump_debug( $input, $collapse = false ) {
	$recursive = function( $data, $level = 0 ) use ( &$recursive, $collapse ) {
		global $argv;

		$is_terminal = isset( $argv );

		if ( ! $is_terminal && 0 === $level && ! defined( 'DUMP_DEBUG_SCRIPT' ) ) {
			define( 'DUMP_DEBUG_SCRIPT', true );
			echo '<script language="Javascript">function toggleDisplay( id ) {';
				echo 'var state = document.getElementById( "container" + id ).style.display;';
				echo 'document.getElementById( "container" + id ).style.display = state == "inline" ? "none" : "inline";';
				echo 'document.getElementById( "plus" + id ).style.display = state == "inline" ? "inline" : "none";';
				echo '}</script>';
				echo "\n";
		}

		$type        = ! is_string( $data ) && is_callable( $data ) ? 'Callable' : ucfirst( gettype( $data ) );
		$type_data   = null;
		$type_color  = null;
		$type_length = null;

		switch ( $type ) {
			case 'String':
				$type_color  = 'green';
				$type_length = strlen( $data );
				$type_data   = '"' . htmlentities( $data ) . '"';
				break;

			case 'Double':
			case 'Float':
				$type        = 'Float';
				$type_color  = '#0099c5';
				$type_length = strlen( $data );
				$type_data   = htmlentities( $data );
				break;

			case 'Integer':
				$type_color  = 'red';
				$type_length = strlen( $data );
				$type_data   = htmlentities( $data );
				break;

			case 'Boolean':
				$type_color  = '#92008d';
				$type_length = strlen( $data );
				$type_data   = $data ? 'TRUE' : 'FALSE';
				break;

			case 'NULL':
				$type_length = 0;
				break;

			case 'Array':
				$type_length = count( $data );
		}

		if ( in_array( $type, array( 'Object', 'Array' ), true ) ) {
			$not_empty = false;

			foreach ( $data as $key => $value ) {
				if ( ! $not_empty ) {
					$not_empty = true;

					if ( $is_terminal ) {
						echo $type . ( null === $type_length ? '(' . $type_length . ')' : '' ) . "\n";

					} else {
						$id = substr( md5( wp_rand() . ':' . $key . ':' . $level ), 0, 8 );

	echo "<a href=\"javascript:toggleDisplay('". $id ."');\" style=\"text-decoration: none;\">";
	echo "<span style='color:#666666'>" . $type . ( $type_length !== null ? '(' . $type_length . ')' : '') . '</span>';
	echo "<span id=\"plus". $id ."\" style=\" color: #ffc600; background: #0273b9; padding: 0.8rem; font-size: 2em; display: " . ( $collapse ? 'inline' : 'none') . ";\">&nbsp;&#10549;</span>";
	echo '</a>';
	echo "<div id=\"container". $id ."\" style=\"padding: 0 25px; font-family: monospace; display: " . ( $collapse ? "" : "inline") . ";\">";
	echo '<br />';
					}

					for ( $i = 0; $i <= $level; $i++ ) {
						echo $is_terminal ? '|    ' : "<span style='color:black'>|</span>" . str_repeat( '&nbsp;', 8 );
					}

					echo $is_terminal ? "\n" : '<br />';
				}

				for ( $i = 0; $i <= $level; $i++ ) {
					echo $is_terminal ? '|    ' : "<span style='color:black'>|</span>" . str_repeat( '&nbsp;', 8 );
				}

				echo $is_terminal ? '[' . $key . "] => " : "<span style='color: black;'>[" . $key . "]&nbsp;=>&nbsp;</span>";

				call_user_func( $recursive, $value, $level + 1 );
			}

			if ( $not_empty ) {
				for ( $i = 0; $i <= $level; $i++ ) {
					echo $is_terminal ? '|    ' : "<span style='color:black'>|</span>" . str_repeat( '&nbsp;', 8 );
				}

				if ( ! $is_terminal ) {
					echo '</div>';
				}

			} else {
				echo $is_terminal ?
						$type . ( null !== $type_length ? '(' . $type_length . ')' : '' ) . '  ' :
						"<span style='color:#666666'>" . $type . ( $type_length !== null ? '(' . $type_length . ')' : '') . '</span>&nbsp;&nbsp;';
			}

		} else {
			echo $is_terminal ?
					$type . ( $type_length !== null ? '(' . $type_length . ')' : '') . '  ' :
					"<span style='color:#666666'>" . $type . ( $type_length !== null ? '(' . $type_length . ')' : '') . '</span>&nbsp;&nbsp;';

			if ( $type_data != null ) {
				echo $is_terminal ? $type_data : "<span style='color:" . $type_color . "'>" . $type_data . '</span>';
			}
		}

		echo $is_terminal ? "\n" : '<br />';
	};

	call_user_func( $recursive, $input );
}
