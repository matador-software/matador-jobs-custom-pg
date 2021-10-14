<?php
/**
 * Matador Jobs Custom Extension for Pierce Gray / Job / Job Description
 *
 * @link        https://matadorjobs.com/
 * @since       1.0.0
 *
 * @package     Matador Jobs Custom Extension for Pierce Gray
 * @subpackage  Core
 * @author      Matador Software LLC, Jeremy Scott, Paul Bearne
 * @copyright   (c) 2021 Matador Software, LLC
 *
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License version 3
 */

namespace matador\MatadorJobsCustomPg\Job;

use matador\Event_Log;
use stdClass;
use matador\Matador;
use matador\Bullhorn_Import;
use matador\MatadorJobsCustomPg\Extension;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Taxonomy
 *
 * @final
 * @since 1.0.0
 */
final class JobDescription {

	/**
	 * Class Constructor
	 *
	 * Adds shortcodes to WP.
	 */
	public function __construct() {
		add_filter( 'matador_bullhorn_import_fields', [ __CLASS__, 'import_fields' ] );
		add_filter( 'matador_bullhorn_import_save_job', [ __CLASS__, 'assemble_description' ], 10, 2 );
	}


	/**
	 * Import Additional Job Description Fields
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public static function import_fields( array $fields ): array {

		$field_to_add = [
			'customTextBlock5' => [
				'name'   => 'the_company',
				'type'   => 'string',
				'saveas' => 'meta',
			],
			'customTextBlock3' => [
				'name'   => 'the_role',
				'type'   => 'string',
				'saveas' => 'meta',
			],
			'customTextBlock4' => [
				'name'   => 'experience_requirements',
				'type'   => 'string',
				'saveas' => 'meta',
			],
		];

		return array_merge( $fields, $field_to_add );
	}

	/**
	 * Import Additional Job Description Fields
	 *
	 * @param stdClass $job
	 * @param int      $wpid
	 *
	 * @return array
	 */
	public static function assemble_description( stdClass $job, int $wpid ): void {

		$company    = isset( $job->customTextBlock5 ) ? wp_kses_post( $job->customTextBlock5 ) : null;
		$role       = isset( $job->customTextBlock3 ) ? wp_kses_post( $job->customTextBlock3 ) : null;
		$experience = isset( $job->customTextBlock4 ) ? wp_kses_post( $job->customTextBlock4 ) : null;

		$description = '';

		if ( $company ) {
			$description .= '<p><strong>' . __( 'The Company', 'matador-extension-custom-pg' ) . '</strong></p>';
			$description .= $company;
		}
		if ( $role ) {
			$description .= '<p><strong>' . __( 'The Role', 'matador-extension-custom-pg' ) . '</strong></p>';
			$description .= $role;
		}
		if ( $experience ) {
			$description .= '<p><strong>' . __( 'Experience & Requirements', 'matador-extension-custom-pg' ) . '</strong></p>';
			$description .= $experience;
		}

		if ( ! empty( $description ) ) {
			wp_update_post( [
				'ID' => (int) $wpid,
				'post_content' => $description,
			] );
		}
	}
}
