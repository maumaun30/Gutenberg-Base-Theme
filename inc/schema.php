<?php
/**
 * Structured data.
 *
 * Yoast owns the @graph and already emits Organization / WebSite / WebPage /
 * BreadcrumbList. Nodes here extend that graph rather than printing their own,
 * so the site never ships two competing entities for the same @id.
 *
 * @package Gutenberg_Base_Theme
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Organization / Casino
 *
 * Enriches the Organization node Yoast builds from SEO -> Site representation.
 * Name, URL, logo and sameAs are managed there; everything below fills the
 * gaps the Yoast UI has no field for, and adds the Casino type plus the PAGCOR
 * accreditation reference.
 * ---------------------------------------------------------------------- */

/**
 * Organisation details with no home in the Yoast UI.
 *
 * @return array
 */
function fnlmx_organization_details() {
	return apply_filters(
		'fnlmx_organization_details',
		[
			'legalName'          => 'Bloomberry Resorts and Hotels, Inc. (Solaire Resorts and Casino)',
			'description'        => 'FUNaloMAX is an online gaming platform offering different categories of entertainment, including casino games, perya games, slot titles, e-games, and more. It is designed to give players convenient access to a variety of gaming options in one place.',
			'foundingDate'       => '2013-03-16',
			'email'              => 'support@funalomax.com',
			'telephone'          => '+632 8888-8888',
			'customerSupport'    => [
				'telephone'         => '+632 8888-8888',
				'email'             => 'support@funalomax.com',
				'availableLanguage' => [ 'en', 'fil' ],
			],
			'priceRange'         => '₱₱',
			'image'              => 'https://games.funalomax.com/wp-content/uploads/2026/08/og-image.webp',
			'logo'               => 'https://games.funalomax.com/wp-content/uploads/2026/06/FUNaloMAX-Logo.svg',
			'sameAs'             => [
				'https://x.com/FUNaloMAX',
				'https://www.facebook.com/FUNaloMAX/',
				'https://www.youtube.com/@FUNaloMAX',
				'https://www.instagram.com/funalomax',
				'https://www.tiktok.com/@funalomax',
			],
			// No parentOrganization on purpose. PAGCOR's accreditation list files
			// FUNaloMAX under "Main Brand" with Bloomberry Resorts and Hotels, Inc.
			// (Solaire Resorts and Casino) as the one accredited administrator --
			// a single company trading under a brand, not a parent and subsidiary.
			// Naming Bloomberry as parent while legalName is also Bloomberry would
			// make the entity its own parent.
			'isCasino'           => true,
			'areaServed'         => 'Philippines',
			'currenciesAccepted' => 'PHP',
			'address'            => [
				'streetAddress'   => '1 Asean Avenue, Entertainment City, Tambo',
				'addressLocality' => 'Parañaque City',
				'addressRegion'   => 'Metro Manila',
				'postalCode'      => '1701',
				'addressCountry'  => 'PH',
			],
		]
	);
}

/**
 * Enrich the Organization node Yoast emits.
 *
 * Every property is conditional: an unset detail is omitted rather than
 * emitted empty.
 *
 * @param array $data Organization schema node.
 * @return array
 */
function fnlmx_filter_schema_organization( $data ) {
	$details = fnlmx_organization_details();

	foreach ( [ 'legalName', 'description', 'foundingDate', 'email', 'telephone' ] as $key ) {
		if ( ! empty( $details[ $key ] ) ) {
			$data[ $key ] = $details[ $key ];
		}
	}

	// addressCountry alone is a default, not a real address.
	$address = array_filter( $details['address'] );
	if ( count( $address ) > 1 ) {
		$data['address'] = [ '@type' => 'PostalAddress' ] + $address;
	}

	$support = array_filter(
		$details['customerSupport'],
		static function ( $value ) {
			return ! empty( $value );
		}
	);
	if ( ! empty( $support['telephone'] ) || ! empty( $support['email'] ) ) {
		$data['contactPoint'] = [
			'@type'       => 'ContactPoint',
			'contactType' => 'customer support',
		] + $support;
	}

	if ( ! empty( $details['parentOrganization']['name'] ) ) {
		$data['parentOrganization'] = [ '@type' => 'Organization' ] + $details['parentOrganization'];
	}

	if ( ! empty( $details['priceRange'] ) ) {
		$data['priceRange'] = $details['priceRange'];
	}

	if ( ! empty( $details['image'] ) ) {
		$data['image'] = $details['image'];
	}

	// Never clobber the richer ImageObject Yoast builds from the UI setting.
	if ( ! empty( $details['logo'] ) && empty( $data['logo'] ) ) {
		$data['logo'] = $details['logo'];
	}

	if ( ! empty( $details['sameAs'] ) ) {
		$same_as = array_merge(
			isset( $data['sameAs'] ) ? (array) $data['sameAs'] : [],
			(array) $details['sameAs']
		);
		$data['sameAs'] = array_values( array_unique( array_filter( $same_as ) ) );
	}

	if ( ! empty( $details['areaServed'] ) ) {
		$data['areaServed'] = [
			'@type' => 'Country',
			'name'  => $details['areaServed'],
		];
	}

	if ( ! empty( $details['currenciesAccepted'] ) ) {
		$data['currenciesAccepted'] = $details['currenciesAccepted'];
	}

	// Casino is a LocalBusiness subtype: merged into the existing node so the
	// site never publishes a second entity for the same business.
	if ( ! empty( $details['isCasino'] ) ) {
		$types = (array) ( $data['@type'] ?? 'Organization' );
		if ( ! in_array( 'Casino', $types, true ) ) {
			$types[] = 'Casino';
		}
		$data['@type'] = count( $types ) > 1 ? array_values( $types ) : reset( $types );
	}

	$listings = fnlmx_regulatory_listings();
	if ( $listings ) {
		$existing          = isset( $data['subjectOf'] ) ? (array) $data['subjectOf'] : [];
		$data['subjectOf'] = array_merge( $existing, $listings );
	}

	return $data;
}
add_filter( 'wpseo_schema_organization', 'fnlmx_filter_schema_organization', 10, 1 );

/**
 * Regulatory documents that list this brand, as subjectOf nodes.
 *
 * Only list a document that actually names this site's brand or domain --
 * citing a register the brand does not appear in is a false claim.
 *
 * @return array
 */
function fnlmx_regulatory_listings() {
	$pagcor = [
		'@type'         => 'GovernmentOrganization',
		'name'          => 'Philippine Amusement and Gaming Corporation',
		'alternateName' => 'PAGCOR',
		'url'           => 'https://www.pagcor.ph/',
	];

	$documents = apply_filters(
		'fnlmx_regulatory_listings',
		[
			[
				'name' => 'List of PAGCOR-Accredited Gaming System Administrators and Registered Brands and Domain Names/URLs',
				'url'  => 'https://www.pagcor.ph/regulatory/pdf/App%20Kits/List-of-PAGCOR-Accredited-Gaming-System-Administrators-and-Registered-Brands-and-Domain-Names-URLs.pdf',
			],
		]
	);

	$nodes = [];
	foreach ( $documents as $document ) {
		if ( empty( $document['url'] ) || empty( $document['name'] ) ) {
			continue;
		}
		$nodes[] = [
			'@type'     => 'DigitalDocument',
			'name'      => $document['name'],
			'url'       => $document['url'],
			'publisher' => $pagcor,
		];
	}

	return $nodes;
}

/* -------------------------------------------------------------------------
 * SoftwareApplication
 *
 * One node per single game page, built from the game's ACF fields. Joins the
 * Yoast graph so publisher can reference the Organization node by @id.
 * ---------------------------------------------------------------------- */

/**
 * Append a SoftwareApplication node on single game pages.
 *
 * @param array $graph Yoast schema graph.
 * @return array
 */
function fnlmx_add_game_software_application( $graph ) {
	if ( ! is_singular( 'game' ) ) {
		return $graph;
	}

	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return $graph;
	}

	$get = static function ( $key ) use ( $post_id ) {
		return function_exists( 'get_field' )
			? get_field( $key, $post_id )
			: get_post_meta( $post_id, $key, true );
	};

	$permalink = get_permalink( $post_id );
	$node      = [
		'@type'               => 'SoftwareApplication',
		'@id'                 => $permalink . '#game',
		'name'                => get_the_title( $post_id ),
		'url'                 => $permalink,
		'applicationCategory' => 'GameApplication',
		'operatingSystem'     => 'Web browser',
	];

	// No offers / aggregateRating node here, so Google will not render a
	// Software App rich result. That is deliberate: these are real-money games
	// with a free demo, and a zero-price Offer would surface a "Free" label
	// that misdescribes the product. Ratings wait until real user ones exist.

	$description = trim( wp_strip_all_tags( (string) $get( 'fnlmx_game_short_description' ) ) );
	if ( '' !== $description ) {
		$node['description'] = $description;
	}

	$image = get_the_post_thumbnail_url( $post_id, 'large' );
	if ( $image ) {
		$node['image'] = $image;
	}

	// The studio that made the game, as distinct from the site operating it.
	$provider = trim( wp_strip_all_tags( (string) $get( 'fnlmx_provider' ) ) );
	if ( '' !== $provider ) {
		$node['author'] = [
			'@type' => 'Organization',
			'name'  => $provider,
		];
	}

	foreach ( $graph as $existing ) {
		if ( ! empty( $existing['@id'] ) && ! empty( $existing['@type'] )
			&& in_array( 'Organization', (array) $existing['@type'], true ) ) {
			$node['publisher'] = [ '@id' => $existing['@id'] ];
			break;
		}
	}

	$terms = get_the_terms( $post_id, 'game_category' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$node['applicationSubCategory'] = $terms[0]->name;
		$node['genre']                  = wp_list_pluck( $terms, 'name' );
	}

	// RTP and volatility have no schema.org equivalents, so they ride along as
	// additionalProperty rather than being forced into an unrelated field.
	$extras = [];
	$rtp    = trim( wp_strip_all_tags( (string) $get( 'fnlmx_rtp' ) ) );
	if ( '' !== $rtp ) {
		$extras[] = [
			'@type' => 'PropertyValue',
			'name'  => 'RTP',
			'value' => $rtp,
		];
	}
	$volatility = trim( wp_strip_all_tags( (string) $get( 'fnlmx_volatility' ) ) );
	if ( '' !== $volatility ) {
		$extras[] = [
			'@type' => 'PropertyValue',
			'name'  => 'Volatility',
			'value' => $volatility,
		];
	}
	if ( $extras ) {
		$node['additionalProperty'] = $extras;
	}

	$graph[] = $node;

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'fnlmx_add_game_software_application', 11, 1 );

/* -------------------------------------------------------------------------
 * HowTo
 *
 * Shared by the Steps block, the Line Steps block and the game rules list on
 * single-game.php -- all three render an ordered title + description sequence.
 * ---------------------------------------------------------------------- */

/**
 * Build a HowTo JSON-LD block from a step list.
 *
 * @param string $name        Heading for the instructions.
 * @param string $description Optional intro copy.
 * @param array  $steps       Step rows with 'title' and/or 'description'.
 * @return string JSON-LD script tag, or '' when there is nothing to describe.
 */
function fnlmx_howto_schema( $name, $description, $steps ) {
	$name = trim( wp_strip_all_tags( (string) $name ) );
	if ( '' === $name || empty( $steps ) || ! is_array( $steps ) ) {
		return '';
	}

	$how_to_steps = [];
	$position     = 0;
	foreach ( $steps as $step ) {
		$step_name = trim( wp_strip_all_tags( (string) ( $step['title'] ?? '' ) ) );
		$step_text = trim( wp_strip_all_tags( (string) ( $step['description'] ?? '' ) ) );

		if ( '' === $step_name && '' === $step_text ) {
			continue;
		}

		++$position;
		$how_to_steps[] = array_filter(
			[
				'@type'    => 'HowToStep',
				'position' => $position,
				'name'     => '' !== $step_name ? $step_name : null,
				// text is required, so fall back to the title when the editor
				// filled only one field in.
				'text'     => '' !== $step_text ? $step_text : $step_name,
			],
			static function ( $value ) {
				return null !== $value;
			}
		);
	}

	// A single step is not a procedure worth describing.
	if ( count( $how_to_steps ) < 2 ) {
		return '';
	}

	// Several step blocks can share a page, so each needs its own @id.
	static $instance = 0;
	++$instance;
	$base = is_singular() ? get_permalink() : home_url( '/' );

	$schema = [
		'@context' => 'https://schema.org',
		'@type'    => 'HowTo',
		'@id'      => $base . '#howto-' . $instance,
		'name'     => $name,
		'step'     => $how_to_steps,
	];

	$description = trim( wp_strip_all_tags( (string) $description ) );
	if ( '' !== $description ) {
		$schema['description'] = $description;
	}

	return '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>';
}
