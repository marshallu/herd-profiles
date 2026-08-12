<?php
/**
 * Shortcodes for Herd Profiles
 *
 * @package herd-profiles
 */

/**
 * Shortcode to display Profile listings.
 *
 * @param array $atts Shortcode attributes.
 *
 * @return string Shortcode output.
 */
function herd_profiles_employee( $atts ) {
	$data = shortcode_atts(
		array(
			'ids'         => false,
			'department'  => false,
			'layout'      => false,
			'site'        => false,
			'two_per_row' => false,
			'per_row'     => '4',
		),
		$atts
	);

	if ( $data['site'] ) {
		switch_to_blog( get_id_from_blogname( $data['site'] ) );
	}

	if ( $data['ids'] ) {
		$ids = trim( $data['ids'] );
		$ids = array_map( 'trim', explode( ',', $ids ) );

		$args = array(
			'post__in'       => $ids,
			'post_type'      => 'employee',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		);
	} else {

		$the_term     = false;
		$dept_listing = false;

		if ( get_field( 'sort_by_last_name_first_name', 'option' ) ) {
			$args = array(
				'post_type'      => 'employee',
				'posts_per_page' => -1,
				'meta_query'     => array( // phpcs:ignore
					'first_name' => array(
						'key' => 'first_name',
					),
					'last_name'  => array(
						'key' => 'last_name',
					),
				),
				'orderby'        => array(
					'menu_order' => 'ASC',
					'last_name'  => 'ASC',
					'first_name' => 'ASC',
					'title'      => 'ASC',
				),
			);
		} else {
			$args = array(
				'post_type'      => 'employee',
				'posts_per_page' => -1,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			);
		}

		if ( $data['department'] ) {
			$args['tax_query'] = array( // phpcs:ignore
				array(
					'taxonomy' => 'department',
					'field'    => 'slug',
					'terms'    => $data['department'],
				),
			);

			$the_term     = get_term_by( 'slug', $data['department'], 'department' );
			$dept_listing = get_field( 'department_listing_display', $the_term );
		}
	}

	$the_query = new WP_Query( $args );

	if ( $data['layout'] ) {
		$display_style = $data['layout'];
	} elseif ( $dept_listing && 'inherit' !== $dept_listing ) {
		$display_style = $dept_listing;
	} elseif ( get_field( 'profile_listing_display', 'option' ) ) {
		$display_style = get_field( 'profile_listing_display', 'option' );
	} else {
		$display_style = 'table';
	}

	$output = '';

	if ( $the_query->have_posts() ) {

		if ( 'row' === $display_style ) {

			if ( $data['two_per_row'] ) {
				$output .= '<div class="flex flex-wrap lg:-mx-6">';
			}

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$image       = get_field( 'employee_headshot' );
				$position    = get_field( 'employee_position' );
				$office      = get_field( 'employee_office_location' );
				$phone       = get_field( 'employee_phone_number' );
				$email       = get_field( 'employee_email_address' );
				$contact_for = get_field( 'employee_contact_for' );

				if ( $data['two_per_row'] ) {
					$output .= '<div class="w-full lg:w-1/2 lg:px-6">';
				}

				$output .= '<div class="flex flex-wrap -mx-2 lg:-mx-6 py-6 border-b border-gray-100 first:mt-0">';
				$output .= '<div class="columns w-full lg:w-1/6 lg:px-6 mt-6 lg:mt-0">';

				if ( get_field( 'employee_headshot' ) ) {
					$output .= '<img src="' . esc_url( $image['sizes']['medium'] ) . '" srcset="' . esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ) . '" alt="' . esc_attr( $image['sizes']['medium'] ) . '" class="rounded-lg" />';
				}

				$output .= '</div>';

				$output .= '<div class="columns w-full lg:w-5/12 lg:px-6 mt-6 lg:mt-0">';

				if ( get_field( 'employee_more_info_link' ) ) {
					$output .= '<strong><a href="' . esc_url( get_field( 'employee_more_info_link' ) ) . '" class="underline hover:no-underline">' . get_the_title() . '</a></strong><br>';
				} elseif ( get_field( 'department_hide_link_to_profile', $the_term ) ) {
					$output .= '<strong>' . get_the_title() . '</strong><br>';
				} else {
					$output .= '<strong><a href="' . esc_url( get_post_permalink() ) . '" rel="noopener noreferrer" class="underline hover:no-underline">' . get_the_title() . '</a></strong><br>';
				}

				$output .= $position . '<br>';

				if ( get_field( 'employee_office_location' ) ) {
					$output .= 'Location: ' . $office . '<br>';
				}

				if ( get_field( 'employee_phone_number' ) ) {
					$output .= 'Telephone: <a href="tel:+1-' . esc_attr( herd_profiles_format_phone( $phone ) ) . '">' . esc_html( herd_profiles_format_phone( $phone ) ) . '</a><br>';
				}

				if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) {
					$output .= 'E-mail: <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
				}

				if ( get_field( 'employee_website' ) ) {
					$output .= '<br><a href="' . esc_url( get_field( 'employee_website' ) ) . '" target="_blank">Website</a>';
				}

				$output .= '</div>';

				$output .= '<div class="columns w-full lg:w-5/12  lg:px-6   mt-6 lg:mt-0">';

				if ( $contact_for ) {
					if ( ! empty( get_field( 'profile_row_title', 'option' ) ) ) {
						$row_title = get_field( 'profile_row_title', 'option' );
					} else {
						$row_title = 'Contact ' . get_the_title() . ' for:';
					}

					$output .= '<strong>' . esc_html( $row_title ) . '</strong>';
					$output .= '<ul>';

					foreach ( $contact_for as $item ) {
						$output .= '<li>' . esc_html( $item['contact_text'] ) . '</li>';
					}

					$output .= '</ul>';
				}
				$output .= '</div>';
				$output .= '</div>';

				if ( $data['two_per_row'] ) {
					$output .= '</div>';
				}
			}

			if ( $data['two_per_row'] ) {
				$output .= '<div class="flex flex-wrap lg:-mx-6">';
			}
		} elseif ( 'enhanced' === $display_style ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$image       = get_field( 'employee_headshot' );
				$position    = get_field( 'employee_position' );
				$office      = get_field( 'employee_office_location' );
				$phone       = get_field( 'employee_phone_number' );
				$email       = get_field( 'employee_email_address' );
				$contact_for = get_field( 'employee_contact_for' );

				$output .= '<div class="marsha-row flex flex-wrap -mx-2 lg:-mx-6 py-6 border-b border-gray-100">';
				$output .= '<div class="columns w-full lg:w-1/6 lg:px-6 mt-6 lg:mt-0">';
				if ( get_field( 'employee_headshot' ) ) {
					$output .= '<img src="' . esc_url( $image['sizes']['medium'] ) . '"  srcset="' . esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ) . '" alt="' . esc_url( $image['alt'] ) . '" class="rounded-lg" />';
				}
				$output .= '</div>';
				$output .= '<div class="columns w-full lg:w-3/4 lg:px-6 mt-6 lg:mt-0 entry-content">';

				if ( get_field( 'department_hide_link_to_profile', $the_term ) ) {
					$output .= '<div class="text-xl font-semibold uppercase">' . get_the_title() . '</div>';
				} else {
					$output .= '<div class="text-xl font-semibold uppercase"><a href="' . esc_url( get_post_permalink() ) . '">' . get_the_title() . '</a></div>';
				}

				if ( get_field( 'employee_preferred_pronouns' ) ) {
						$output .= '<div class="flex items-center my-2">Preferred Pronouns: ' . esc_html( get_field( 'employee_preferred_pronouns' ) ) . '</div>';
				}

				$output .= '<div class="mt-3 mb-4">';
				if ( get_field( 'employee_position' ) ) {
					$output .= '<div class="font-semibold mb-1">' . $position . '</div>';
				}

				$output .= herd_profiles_department_listing( get_the_ID(), true );

				$output .= '</div>';
				if ( get_field( 'employee_office_location' ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="text-gray-500 fill-current h-4 w-4 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"></path></svg>';
					$output .= $office;
					$output .= '</div>';
				}
				if ( get_field( 'employee_phone_number' ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="text-gray-500 fill-current h-4 w-4 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg>';
					$output .= esc_html( herd_profiles_format_phone( $phone ) );
					$output .= '</div>';
				}
				if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'profile' === get_field( 'profile_show_email_address', 'option' ) ) ) {
					$output     .= '<div class="flex items-center my-2">';
					$output     .= '<svg class="text-gray-200 fill-current h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>';
						$output .= '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
					$output     .= '</div>';
				}

				if ( get_field( 'employee_website' ) ) {
					$output     .= '<div class="flex items-center my-2">';
					$output     .= '<svg class="text-gray-200 fill-current h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path fill="currentColor" d="M336.5 160C322 70.7 287.8 8 248 8s-74 62.7-88.5 152h177zM152 256c0 22.2 1.2 43.5 3.3 64h185.3c2.1-20.5 3.3-41.8 3.3-64s-1.2-43.5-3.3-64H155.3c-2.1 20.5-3.3 41.8-3.3 64zm324.7-96c-28.6-67.9-86.5-120.4-158-141.6 24.4 33.8 41.2 84.7 50 141.6h108zM177.2 18.4C105.8 39.6 47.8 92.1 19.3 160h108c8.7-56.9 25.5-107.8 49.9-141.6zM487.4 192H372.7c2.1 21 3.3 42.5 3.3 64s-1.2 43-3.3 64h114.6c5.5-20.5 8.6-41.8 8.6-64s-3.1-43.5-8.5-64zM120 256c0-21.5 1.2-43 3.3-64H8.6C3.2 212.5 0 233.8 0 256s3.2 43.5 8.6 64h114.6c-2-21-3.2-42.5-3.2-64zm39.5 96c14.5 89.3 48.7 152 88.5 152s74-62.7 88.5-152h-177zm159.3 141.6c71.4-21.2 129.4-73.7 158-141.6h-108c-8.8 56.9-25.6 107.8-50 141.6zM19.3 352c28.6 67.9 86.5 120.4 158 141.6-24.4-33.8-41.2-84.7-50-141.6h-108z"></path></svg>';
					$output     .= '<svg class="text-gray-200 fill-current h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><path fill="currentColor" d="M336.5 160C322 70.7 287.8 8 248 8s-74 62.7-88.5 152h177zM152 256c0 22.2 1.2 43.5 3.3 64h185.3c2.1-20.5 3.3-41.8 3.3-64s-1.2-43.5-3.3-64H155.3c-2.1 20.5-3.3 41.8-3.3 64zm324.7-96c-28.6-67.9-86.5-120.4-158-141.6 24.4 33.8 41.2 84.7 50 141.6h108zM177.2 18.4C105.8 39.6 47.8 92.1 19.3 160h108c8.7-56.9 25.5-107.8 49.9-141.6zM487.4 192H372.7c2.1 21 3.3 42.5 3.3 64s-1.2 43-3.3 64h114.6c5.5-20.5 8.6-41.8 8.6-64s-3.1-43.5-8.5-64zM120 256c0-21.5 1.2-43 3.3-64H8.6C3.2 212.5 0 233.8 0 256s3.2 43.5 8.6 64h114.6c-2-21-3.2-42.5-3.2-64zm39.5 96c14.5 89.3 48.7 152 88.5 152s74-62.7 88.5-152h-177zm159.3 141.6c71.4-21.2 129.4-73.7 158-141.6h-108c-8.8 56.9-25.6 107.8-50 141.6zM19.3 352c28.6 67.9 86.5 120.4 158 141.6-24.4-33.8-41.2-84.7-50-141.6h-108z"></path></svg>';
						$output .= '<a href="' . esc_url( get_field( 'employee_website' ) ) . '">Visit Website</a>';
					$output     .= '</div>';
				}

				if ( get_field( 'employee_bookings_url' ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="fill-gray-200 h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z"/></svg>';
					$output .= '<a href="' . esc_url( get_field( 'employee_bookings_url' ) ) . '">Book a Meeting</a>';
					$output .= '</div>';
				}

				$output .= '</div>';
				$output .= '</div>';
			}
		} elseif ( 'card' === $display_style ) {
			$output .= '<div class="flex flex-wrap mx-0 lg:-mx-4">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$image    = get_field( 'employee_headshot' );
				$position = get_field( 'employee_position' );
				$office   = get_field( 'employee_office_location' );
				$phone    = get_field( 'employee_phone_number' );
				$email    = get_field( 'employee_email_address' );

				if ( $data['two_per_row'] ) {
					$output .= '<div class="w-full lg:w-1/2 px-0 lg:px-4 mb-4 lg:mb-8 flex flex-row">';
					$output .= '<div class="flex flex-wrap -mx-2 lg:-mx-6 py-6 border-b border-gray-100 first:mt-0 w-full">';

					$output .= '<div class="columns w-full lg:w-1/3 lg:px-6 mt-6 lg:mt-0">';
					if ( get_field( 'employee_headshot' ) ) {
						$output .= '<img src="' . esc_url( $image['sizes']['medium'] ) . '" srcset="' . esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ) . '" alt="' . esc_attr( $image['sizes']['medium'] ) . '" class="rounded-lg w-full" />';
					}
					$output .= '</div>';

					$output .= '<div class="columns w-full lg:w-2/3 lg:px-6 mt-6 lg:mt-0">';
					if ( get_field( 'employee_more_info_link' ) ) {
						$output .= '<strong><a href="' . esc_url( get_field( 'employee_more_info_link' ) ) . '" class="underline hover:no-underline">' . get_the_title() . '</a></strong><br>';
					} elseif ( get_field( 'department_hide_link_to_profile', $the_term ) ) {
							$output .= '<strong>' . get_the_title() . '</strong><br>';
					} else {
						$output .= '<strong><a href="' . esc_url( get_post_permalink() ) . '" rel="noopener noreferrer" class="underline hover:no-underline">' . get_the_title() . '</a></strong><br>';
					}

					$output .= $position . '<br>';

					if ( get_field( 'employee_office_location' ) ) {
						$output .= 'Location: ' . $office . '<br>';
					}

					if ( get_field( 'employee_phone_number' ) ) {
						$output .= 'Telephone: <a href="tel:+1-' . esc_attr( herd_profiles_format_phone( $phone ) ) . '">' . esc_html( herd_profiles_format_phone( $phone ) ) . '</a><br>';
					}

					if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) {
						$output .= 'E-mail: <a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
					}

					if ( get_field( 'employee_website' ) ) {
						$output .= '<br><a href="' . esc_url( get_field( 'employee_website' ) ) . '" target="_blank">Website</a>';
					}
					$output .= '</div>';

					$output .= '</div>';

					$output .= '</div>';
				} else {
					$output .= '<div class="w-full lg:w-1/3 px-0 lg:px-4 mb-4 lg:mb-8 flex flex-row">';
					$output .= '<div class="w-full bg-gray-100 border border-gray-200 px-4 py-4">';

					if ( get_field( 'profile_link_to_profile', 'option' ) ) {
						$output .= '<div class="text-xl font-semibold"><a href="' . esc_url( get_post_permalink() ) . '">' . get_the_title() . '</a></div>';
					} else {
						$output .= '<div class="text-xl font-semibold">' . get_the_title() . '</div>';
					}

					$output .= '<div class="pt-3 grid grid-cols-3 gap-4">';
					$output .= '<div class="">';

					if ( get_field( 'employee_headshot' ) ) {
						$output .= '<img src="' . esc_url( $image['url'] ) . '" srcset="' . esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ) . '" alt="' . esc_attr( $image['alt'] ) . '" class="w-full rounded-lg" />';
					}
					$output .= '</div>';

					$output .= '<div class="col-span-2">';
					if ( get_field( 'employee_position' ) ) {
						$output .= '<div class="font-semibold">' . get_field( 'employee_position' ) . '</div>';
					}

					if ( get_field( 'employee_office_location' ) ) {
						$output .= '<div>Office: ' . get_field( 'employee_office_location' ) . '</div>';
					}

					if ( get_field( 'employee_phone_number' ) ) {
						$output .= '<div>Phone: ' . ( herd_profiles_format_phone( get_field( 'employee_phone_number' ) ) ) . '</div>';
					}

					if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) {
						$output .= '<div class="hidden xl:block"><a href="mailto:' . get_field( 'employee_email_address' ) . '">' . get_field( 'employee_email_address' ) . '</a></div>';
						$output .= '<div class="block xl:hidden"><a href="mailto:' . get_field( 'employee_email_address' ) . '">Email</a></div>';
					}

					if ( get_field( 'employee_website' ) ) {
						$output .= '<div><a href="' . get_field( 'employee_website' ) . '">Visit Website</a></div>';
					}
					$output .= '</div>';
					$output .= '</div>';
					$output .= '</div>';
					$output .= '</div>';
				}
			}
			$output .= '</div>';
		} elseif ( 'basic' === $display_style ) {
			if ( is_page_template( array( 'page-full-width.php', 'page-full-width-hero.php', 'page-secondary-nav.php', 'page-secondary-classic.php', 'page-experience.php' ) ) ) {
				$width = ' lg:w-1/3 ';
			} else {
				$width = ' lg:w-1/2 ';
			}
			$output .= '<div class="">';
			$output .= '<div class="flex flex-wrap lg:-mx-6">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$image = get_field( 'employee_headshot' );

				$output .= '<div class="w-full ' . esc_attr( $width ) . ' lg:px-6 mb-8">';
				$output .= '<div class="flex flex-wrap flex-row lg:-mx-2">';
				$output .= '<div class="w-full lg:w-1/4 lg:px-2">';
				$output .= '<img class="object-cover rounded-lg" src="' . esc_url( $image['url'] ) . '"  srcset="' . esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ) . '" alt="' . esc_attr( $image['alt'] ) . '" />';
				$output .= '</div>';
				$output .= '<div class="w-full lg:w-3/4 lg:px-2 mt-4 lg:mt-0">';
				$output .= '<div class="text-lg font-semibold space-y-1">';
				$output .= '<div>' . get_the_title() . '</div>';
				$output .= '<p class="text-gray-500">' . get_field( 'employee_position' ) . '</p>';
				$output .= '</div>';
				$output .= '<div class="text-lg mt-1">';
				$output .= '<p class="text-gray-500">' . get_field( 'employee_biography' ) . '</p>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';

			}
			$output .= '</div>';
			$output .= '</div>';
		} elseif ( 'full-profile' === $display_style ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$image       = get_field( 'employee_headshot' );
				$position    = get_field( 'employee_position' );
				$office      = get_field( 'employee_office_location' );
				$phone       = get_field( 'employee_phone_number' );
				$email       = get_field( 'employee_email_address' );
				$contact_for = get_field( 'employee_contact_for' );

				$output .= '<div class="marsha-row flex flex-wrap -mx-2 lg:-mx-6 py-6 border-b border-gray-100">';
				$output .= '<div class="columns w-full lg:w-1/6 lg:px-6 mt-6 lg:mt-0">';
				if ( get_field( 'employee_headshot' ) ) {
					$output .= '<img src="' . esc_url( $image['sizes']['medium'] ) . '"  srcset="' . esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ) . '" alt="' . esc_url( $image['alt'] ) . '" class="rounded-lg" />';
				}
				$output .= '</div>';
				$output .= '<div class="columns w-full lg:w-3/4 lg:px-6 mt-6 lg:mt-0 entry-content">';

				$output .= '<div class="text-xl font-semibold uppercase">' . get_the_title() . '</div>';

				$output .= '<div class="mt-3 mb-4">';

				if ( get_field( 'employee_position' ) ) {
					$output .= '<div class="font-semibold mb-1">' . $position . '</div>';
				}

				$output .= herd_profiles_department_listing( get_the_ID(), true );

				$output .= '</div>';
				if ( get_field( 'employee_office_location' ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="text-gray-200 fill-current h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"></path></svg>';
					$output .= $office;
					$output .= '</div>';
				}
				if ( get_field( 'employee_phone_number' ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="text-gray-200 fill-current h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg>';
					$output .= herd_profiles_format_phone( $phone );
					$output .= '</div>';
				}
				if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'profile' === get_field( 'profile_show_email_address', 'option' ) ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="text-gray-200 fill-current h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>';
					$output .= '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
					$output .= '</div>';
				}

				if ( get_field( 'employee_bookings_url' ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="fill-gray-200 h-5 w-5 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M1 4c0-1.1.9-2 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V4zm2 2v12h14V6H3zm2-6h2v2H5V0zm8 0h2v2h-2V0zM5 9h2v2H5V9zm0 4h2v2H5v-2zm4-4h2v2H9V9zm0 4h2v2H9v-2zm4-4h2v2h-2V9zm0 4h2v2h-2v-2z"/></svg>';
					$output .= '<a href="' . esc_url( get_field( 'employee_bookings_url' ) ) . '">Book a Meeting</a>';
					$output .= '</div>';
				}

				$output .= '<div class="mt-6">' . get_field( 'employee_biography' ) . '</div>';
				$output .= '</div>';
				$output .= '</div>';
			}
		} elseif ( 'icon-card' === $display_style ) {
			$output .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">';
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$image    = get_field( 'employee_headshot' );
				$position = get_field( 'employee_position' );
				$office   = get_field( 'employee_office_location' );
				$phone    = get_field( 'employee_phone_number' );
				$email    = get_field( 'employee_email_address' );

				$output .= '<div class="h-full bg-white flex flex-col rounded-sm shadow-md border border-gray-50 ring-1 ring-gray-50/50 ">';

				if ( get_field( 'employee_headshot' ) ) {
					$output .= '<img src="' . esc_url( $image['sizes']['medium'] ) . '"  srcset="' . esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ) . '" alt="' . esc_url( $image['alt'] ) . '"  class="rounded-t block w-full" loading="lazy" />';
				}

				$output .= '<div class="h-full flex flex-col pb-8 px-6">';

				$output .= '<div class="flex items-start pt-6">';
				$output .= '<svg class="h-6 w-6 mt-2 fill-green mr-4 " xmlns="http://www.w3.org/2000/svg" viewBox="0 0 250.96 166.04"><defs></defs><path class="cls-1" d="M85.99 117.3v-17H51.41v17c0 7.66-7.06 18.58-14.35 18.58v14.86h63.27v-14.86c-7.05 0-14.34-10.92-14.34-18.58zM51.41 44.05v21.78h68.84L90.32 15.89H37.06v12.59c7.29-.02 14.35 7.64 14.35 15.57zm109.34-28.18l-30 49.94h68.84V44.05c0-7.91 7.06-15.57 14.34-15.57V15.87zM96.92 100.32l28.6 47.09 28.59-47.09H96.92zM199.63 117.3v-17h-34.58v17c0 7.66-7.29 18.58-14.35 18.58v14.86h63.27v-14.86c-7.28 0-14.34-10.92-14.34-18.58z"></path><path class="cls-2" d="M213.95 62.05v-8.6c0-4.33 0-7.73 1.55-9.27 1.38-1.38 4.57-2.08 9.49-2.08h3.76V.05h-76.79l-26.44 43.84L99.07.05H22.28v42.1h3.76c4.92 0 8.11.7 9.5 2.08 1.53 1.54 1.54 4.95 1.54 9.27v8.55H0v42h37.08v3.17c0 5.28 0 10.6-2.76 13.34-1.63 1.64-4.41 2.46-8.28 2.46h-3.76v43h92.88v-6.2l3.72 6.2h13.27s2.69-4.46 3.73-6.2v6.23h92.87v-43h-3.76c-3.86 0-6.65-.82-8.28-2.46-2.74-2.74-2.76-8.06-2.76-13.23v-3.31h37v-42zm33.25 38.27h-37v6.83c0 9-.06 19.66 14.8 19.66v35.48h-85.36v-16l-9.61 16h-9l-9.61-16v16H26.04v-35.48c14.86 0 14.8-10.64 14.8-19.66v-6.83H3.75V65.81h37.08V53.67c0-9.32.06-15.33-14.8-15.33V3.76h70.92l28.57 47.36 28.56-47.36h70.91v34.58c-14.86 0-14.8 6-14.8 15.33v12.14h37z"></path><path class="cls-2" d="M154.11 100.32l-28.59 47.09-28.6-47.09h-5.34l33.94 56.25 33.93-56.25h-5.34zM90.02 113.65v-13.33h-4v17c0 7.66 7.29 18.58 14.34 18.58v14.86h-63.3v-14.88c7.29 0 14.35-10.92 14.35-18.58v-17h-4.15v13.35c0 9.12-5.23 19-14.62 19v22.53h72.11V132.7c-8.95 0-14.73-9.36-14.73-19.05zM47.26 47.44v18.37h4.15V44.05c0-7.91-7.06-15.57-14.35-15.57V15.87h53.26l29.93 49.94h10.5l30-49.94h53.26v12.59c-7.28 0-14.34 7.66-14.34 15.57v21.78h4.08V47.44c0-8.46 5.23-15.09 14.62-15.09v-21.2h-60.2l-32.68 54.41-32.66-54.41H32.64v21.2c9.39 0 14.62 6.63 14.62 15.09zm156.49 66.21v-13.33h-4.15v17c0 7.66 7.06 18.58 14.34 18.58v14.86h-63.19v-14.88c7.06 0 14.35-10.92 14.35-18.58v-17h-4v13.35c0 9.69-5.78 19-14.72 19v22.53h72.1V132.7c-9.47 0-14.73-9.93-14.73-19.05zm-160-17.96h2.87l7.13-16.29v11.16c0 2.56 0 2.92-2.88 3v2.14h11.2v-2c-2.4-.35-3.11-1.3-3.11-3.19V76.75a10.62 10.62 0 01.19-3.15c.2-.4.64-.83 2.92-1.15v-2h-8.16L45.98 88.2l-8.44-17.75h-8v2c2.76.36 2.88.79 2.88 2.09 0 1.66.08 10.89 0 14.52-.12 4-.55 4.26-2.88 4.61v2h9.23v-2c-2.76-.35-3-.87-3-3.51V79.05zm33.8-2.02v2h9.31v-2.12c-1.65 0-2.13-.27-2.72-1.85l-8-21.69h-2.8l-7.69 21.22c-.47 1.3-.9 2.21-2.52 2.44v2h8v-2.12c-1.7-.07-1.86-.31-1.86-1.1a24.6 24.6 0 011.22-4.77h7.26a33.6 33.6 0 011.46 5.25c0 .23 0 .47-1.66.74zm-6-9.18l2.4-7.23 2.57 7.23zm26.5 1.38c.51 0 .91-.08 1.3-.08a52.17 52.17 0 005.72 8.44 5.59 5.59 0 004.34 1.93 16.29 16.29 0 003.63-.55v-2a4.48 4.48 0 01-3.87-1.62 47.19 47.19 0 01-5.4-7.86 9 9 0 003.55-6.93c0-6.63-6.51-7.18-9.66-7.18-2.72 0-7.14.31-9.75.47v2.13c2.61 0 3 .67 3 3.87v13.88c0 1.5-.12 3-3.07 3.27v2h10.61v-2.09c-2.45-.11-2.8-1.33-2.8-2.68v-5zm-2.4-10.15c0-.19.08-1.65.11-2.6a14.17 14.17 0 011.82-.15c2.52 0 4.73 1.42 4.73 5 0 4.15-2 5.05-4.65 5.05h-2zm27.16 17.33c-1.78 0-6.47-.43-6.59-5.8h-2.13v7.19a21.57 21.57 0 008.64 1.93c6.79 0 8.52-4 8.52-6.94a7.37 7.37 0 00-2.33-5.65c-1.93-2-4.53-3-6.82-4.42-1.5-.94-2.64-1.89-2.64-3.63 0-1 .55-2.64 3.78-2.64 3.71 0 4.62 1.74 4.86 5.45h2.25v-8.05c-3-.16-3.05-.71-7.66-.71-5.29 0-8 3.15-8 6.9 0 4.42 3.59 6.51 6.71 8.21a18.59 18.59 0 013.94 2.48 3.14 3.14 0 011 2.49c.06 1.56-.77 3.19-3.53 3.19zm35.24-20.4v-2.09h-10.64v2.09c2.4 0 3 .27 3 5.09v2.91h-10.5v-2.91c0-4.82.59-5.05 3-5.09v-2.09h-10.65v2.09c2.52 0 3 0 3 3.9v11.56c0 4.85-.44 5-3 5.56v2h10.65v-2c-2.36-.39-3-.71-3-2.76v-7.1h10.5v7.1c0 2.05-.64 2.37-3 2.76v2h10.64v-2c-2.56-.55-3-.71-3-5.56V76.55c.01-3.86.49-3.9 3-3.9zm15.46 21.02v2h9.3v-2.12c-1.65 0-2.12-.27-2.71-1.85l-8-21.69h-2.81l-7.69 21.22c-.47 1.3-.9 2.21-2.52 2.44v2h8v-2.12c-1.7-.07-1.86-.31-1.86-1.1a23.94 23.94 0 011.23-4.77h7.3a33.6 33.6 0 011.46 5.25c-.05.23-.05.47-1.7.74zm-6-9.18l2.41-7.23 2.56 7.23zm16.31 9.06v2.14h18.27v-7.3h-1.93c-.83 3.87-2.33 4.34-5.13 4.34-3.08 0-3.27-.63-3.27-2.41V75.81c.08-2.23.27-2.71.8-2.89a11.84 11.84 0 012.32-.39v-2h-11.06v2.13c2.22 0 3 .16 3 6v7.86c0 6.64-.82 7.12-3 7.03zm19.29-20.86c2.2 0 3 .16 3 6v7.86c0 6.58-.83 7.06-3 7v2.14h18.26v-7.3h-1.94c-.83 3.87-2.32 4.34-5.12 4.34-3.08 0-3.28-.63-3.28-2.41V75.81c.07-2.23.26-2.71.79-2.89a12.07 12.07 0 012.33-.39v-2h-11.04zM230.65 156.97h2.79v8.42h1.01v-8.42h2.94v-1.01h-6.74v1.01zm12.13 6.66l-2.42-7.67h-1.62v9.43h1.01v-8.01l2.53 8.01h1.01l2.52-8.01v8.01h1.01v-9.43h-1.62l-2.42 7.67z"></path><path class="cls-3" d="M247.2 100.32V65.81h-37V53.67c0-9.32-.06-15.33 14.8-15.33V3.76h-70.92l-28.56 47.36L96.95 3.76H26.04v34.58c14.86 0 14.8 6 14.8 15.33v12.14H3.75v34.51h37.08v6.83c0 9 .06 19.66-14.8 19.66v35.48h85.36v-16l9.61 16h9l9.61-16v16h85.35v-35.48c-14.86 0-14.8-10.64-14.8-19.66v-6.83zm-214.56-68V11.15h60.19l32.69 54.41 32.68-54.41h60.2v21.2c-9.39 0-14.62 6.63-14.62 15.09v18.37H47.26V47.44c0-8.46-5.23-15.09-14.62-15.09zm151.18 40.34v-2.13h11.09v2a11.84 11.84 0 00-2.32.39c-.53.18-.72.66-.8 2.89v14.51c0 1.78.19 2.41 3.27 2.41 2.8 0 4.3-.47 5.13-4.34h1.93v7.3h-18.3v-2.14c2.18.09 3-.39 3-7v-7.86c0-5.84-.78-5.96-3-6zm-10.12 15h-7.25a23.94 23.94 0 00-1.23 4.77c0 .79.16 1 1.86 1.1v2.14h-8v-2c1.62-.23 2.05-1.14 2.52-2.44l7.68-21.18h2.81l8 21.69c.59 1.58 1.06 1.85 2.71 1.85v2.14h-9.3v-2c1.65-.27 1.65-.51 1.65-.74a33.6 33.6 0 00-1.4-5.31zm-15.65 6v2h-10.64v-2c2.36-.39 3-.71 3-2.76v-7.1h-10.5v7.1c0 2.05.64 2.37 3 2.76v2h-10.65v-2c2.56-.55 3-.71 3-5.56V76.55c0-3.86-.48-3.9-3-3.9v-2.09h10.65v2.09c-2.41 0-3 .27-3 5.09v2.91h10.5v-2.91c0-4.82-.6-5.05-3-5.09v-2.09h10.64v2.09c-2.51 0-3 0-3 3.9v11.56c.01 4.85.44 5.01 3 5.56zm-32.68-6.31a18.59 18.59 0 00-3.94-2.48c-3.12-1.7-6.71-3.79-6.71-8.21 0-3.75 2.68-6.9 8-6.9 4.61 0 4.65.55 7.66.71v8.05h-2.25c-.24-3.71-1.15-5.45-4.86-5.45-3.23 0-3.78 1.66-3.78 2.64 0 1.74 1.14 2.69 2.64 3.63 2.29 1.43 4.89 2.45 6.82 4.42a7.37 7.37 0 012.33 5.65c0 2.95-1.73 6.94-8.52 6.94a21.57 21.57 0 01-8.64-1.93v-7.19h2.13c.12 5.37 4.81 5.8 6.59 5.8 2.76 0 3.59-1.62 3.59-3.19a3.14 3.14 0 00-1.06-2.48zm-26.92 6.2v2.14H87.88v-2c2.95-.31 3.07-1.77 3.07-3.27v-13.9c0-3.2-.39-3.87-3-3.87v-2.13c2.61-.16 7-.47 9.75-.47 3.15 0 9.66.55 9.66 7.18a9 9 0 01-3.55 6.93 47.19 47.19 0 005.4 7.86 4.48 4.48 0 003.87 1.62v2a16.29 16.29 0 01-3.63.55 5.59 5.59 0 01-4.34-1.93 52.17 52.17 0 01-5.72-8.44c-.39 0-.79.08-1.3.08h-2.4v5c-.04 1.32.31 2.54 2.76 2.65zm-20.7-5.87h-7.26a24.6 24.6 0 00-1.22 4.77c0 .79.16 1 1.86 1.1v2.14h-8v-2c1.62-.23 2.05-1.14 2.52-2.44l7.68-21.2h2.8l8 21.69c.59 1.58 1.07 1.85 2.72 1.85v2.14h-9.3v-2c1.66-.27 1.66-.51 1.66-.74a33.6 33.6 0 00-1.46-5.31zm-38.92 6v2H29.6v-2c2.33-.35 2.76-.59 2.88-4.61.08-3.63 0-12.86 0-14.52 0-1.3-.12-1.73-2.88-2.09v-2h8l8.44 17.75 7.93-17.75h8.16v2c-2.28.32-2.72.75-2.92 1.15a10.62 10.62 0 00-.19 3.15v13.73c0 1.89.71 2.84 3.11 3.19v2h-11.2v-2.13c2.84-.07 2.88-.43 2.88-3V79.4l-7.14 16.29h-2.92l-8-16.64v11.16c0 2.59.32 3.11 3.08 3.46zm65.92 61.55H32.64V132.7c9.39 0 14.62-9.93 14.62-19v-13.38h42.76v13.33c0 9.69 5.78 19 14.71 19zm20.79 1.35l-33.93-56.26h67.84zm92.88-23.88v22.53h-72.1V132.7c8.94 0 14.72-9.36 14.72-19v-13.38h42.71v13.33c0 9.12 5.26 19.05 14.65 19.05zm-15.29-37v-2.15c2.16.09 3-.39 3-7v-7.86c0-5.87-.79-6-3-6v-2.13h11.08v2a12.07 12.07 0 00-2.33.39c-.53.18-.72.66-.79 2.89v14.51c0 1.78.2 2.41 3.28 2.41 2.8 0 4.29-.47 5.12-4.34h1.94v7.3z"></path><path class="cls-3" d="M102.31 77.97c0-3.58-2.21-5-4.73-5a14.17 14.17 0 00-1.82.15c0 .95-.11 2.41-.11 2.6v7.33h2c2.69 0 4.66-.93 4.66-5.08zM71.55 84.48h4.98l-2.57-7.22-2.41 7.22zm95.96 0h4.97l-2.57-7.22-2.4 7.22z"></path></svg>';
				$output .= '<div class="font-semibold text-2xl text-gray-900">' . get_the_title() . '</div>';
				$output .= '</div>';

				$output .= '<div class="my-2 flex-1 border-t border-gray-100 mt-6 pt-6 text-gray-600 font-normal">';
				if ( get_field( 'employee_position' ) ) {
					$output .= '<div class="mt-1">' . esc_html( $position ) . '</div>';
				}

				if ( get_field( 'employee_office_location' ) ) {
					$output .= '<div class="mt-1">Location: ' . esc_attr( $office ) . '</div>';
				}

				if ( get_field( 'employee_phone_number' ) ) {
					$output .= '<div class="mt-1">Telephone: <a href="tel:+1-' . esc_attr( herd_profiles_format_phone( $phone ) ) . '">' . esc_html( herd_profiles_format_phone( $phone ) ) . '</a></div>';
				}

				if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) {
					$output .= '<div class=" my-2">Email: <a href="mailto:' . $email . '">' . $email . '</a></div>';
				}

				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
			}
			$output .= '</div>';
		} elseif ( 'sidebar' === $display_style ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$image    = get_field( 'employee_headshot' );
				$position = get_field( 'employee_position' );
				$office   = get_field( 'employee_office_location' );
				$phone    = get_field( 'employee_phone_number' );
				$email    = get_field( 'employee_email_address' );

				$output .= '<div class="mb-8">';
				$output .= '<div class="font-bold">' . get_the_title() . '</div>';

				if ( get_field( 'employee_position' ) ) {
					$output .= '<div class="mt-1">' . esc_html( $position ) . '</div>';
				}

				if ( get_field( 'employee_phone_number' ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="text-green fill-current h-4 w-4 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg>';
					$output .= '<div>' . esc_html( herd_profiles_format_phone( $phone ) ) . '</div>';
					$output .= '</div>';
				}
				if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'profile' === get_field( 'profile_show_email_address', 'option' ) ) ) {
					$output .= '<div class="flex items-center my-2">';
					$output .= '<svg class="text-green fill-current h-4 w-4 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>';
					$output .= '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>';
					$output .= '</div>';
				}
				$output .= '</div>';
			}
		} else {
			$output .= '<div class="large-table">';
			$output .= '<table class="table table-striped table-bordered w-full">';
			$output .= '<thead>';
			$output .= '<tr class="">';
			$output .= '<th>Name</th>';
			$output .= '<th>Title</th>';
			$output .= '<th>Office</th>';
			$output .= '<th>Phone</th>';
			if ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'profile' === get_field( 'profile_show_email_address', 'option' ) ) {
				$output .= '<th>Email</th>';
			}
			$output .= '</tr>';
			$output .= '</thead>';
			$output .= '<tbody>';

			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$position = get_field( 'employee_position', get_the_ID() );
				$office   = get_field( 'employee_office_location', get_the_ID() );
				$phone    = get_field( 'employee_phone_number', get_the_ID() );
				$email    = get_field( 'employee_email_address', get_the_ID() );

				$output .= '<tr class="">';

				if ( get_field( 'department_hide_link_to_profile', $the_term ) ) {
					$output .= '<td class="">' . esc_html( get_the_title() ) . '</td>';
				} else {
					$output .= '<td class=""><a href="' . esc_url( get_post_permalink() ) . '" rel="noopener noreferrer">' . esc_html( get_the_title() ) . '</a></td>';
				}

				$output .= '<td class="">' . $position . '</td>';
				$output .= '<td class="">' . $office . '</td>';
				$output .= '<td class="whitespace-nowrap">' . esc_html( herd_profiles_format_phone( get_field( 'employee_phone_number', get_the_ID() ) ) ) . '</td>';
				if ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'profile' === get_field( 'profile_show_email_address', get_the_ID(), 'option' ) ) {
					$output .= '<td class="whitespace-nowrap"><a href="mailto:' . esc_attr( get_field( 'employee_email_address', get_the_ID() ) ) . '" rel="noopener noreferrer">' . esc_html( get_field( 'employee_email_address', get_the_ID() ) ) . '</a></td>';
				}

				$output .= '</tr>';
			}

			$output .= '</tbody>';
			$output .= '</table>';
			$output .= '</div>';
		}
	} else {
		$output = 'No profiles found for this category.';
	}

	if ( $data['site'] ) {
		restore_current_blog();
	}
	wp_reset_postdata();
	return $output;
}
add_shortcode( 'herd_employee', 'herd_profiles_employee' );
add_shortcode( 'herd_profiles', 'herd_profiles_employee' );
add_shortcode( 'herd_profile', 'herd_profiles_employee' );
add_shortcode( 'mu_employee', 'herd_profiles_employee' );
add_shortcode( 'mu_profiles', 'herd_profiles_employee' );
add_shortcode( 'mu_profile', 'herd_profiles_employee' );
