<?php

function arve_get_default_aspect_ratio( $aspect_ratio, $provider ) {
	$properties = arve_get_host_properties();
	if ( empty( $aspect_ratio ) ) {
		return $properties[ $provider ]['aspect_ratio'];
	}
	return $aspect_ratio;
}

function arve_get_html5_attributes() {

	return array( 'mp4', 'm4v', 'webm', 'ogv', 'ogg', 'ogm' );
}

function arve_url_query_array( $url ) {

	$url = wp_parse_url( $url );

	if ( empty( $url['query'] ) ) {
		return array();
	}

	parse_str( $url['query'], $url_params );

	return $url_params;
}

function arve_build_iframe_src( $atts ) {

	$id         = $atts['id'];
	$lang       = $atts['lang'];
	$provider   = $atts['provider'];
	$options    = arve_get_options();
	$properties = arve_get_host_properties();

	if ( $options['youtube_nocookie'] ) {
		$properties['youtube']['embed_url']     = 'https://www.youtube-nocookie.com/embed/%s';
		$properties['youtubelist']['embed_url'] = 'https://www.youtube-nocookie.com/embed/videoseries?list=%s';
	}

	if ( isset( $properties[ $provider ]['embed_url'] ) ) {
		$pattern = $properties[ $provider ]['embed_url'];
	} else {
		$pattern = '%s';
	}

	if ( 'facebook' === $provider && is_numeric( $id ) ) {

		$id = "https://www.facebook.com/facebook/videos/$id/";

	} elseif ( 'twitch' === $provider && is_numeric( $id ) ) {

		$pattern = 'https://player.twitch.tv/?video=v%s';

	} elseif ( 'ted' === $provider && preg_match( '/^[a-z]{2}$/', $lang ) === 1 ) {

		$pattern = 'https://embed-ssl.ted.com/talks/lang/' . $lang . '/%s.html';
	}

	if ( isset( $properties[ $provider ]['url_encode_id'] ) && $properties[ $provider ]['url_encode_id'] ) {
		$id = rawurlencode( $id );
	}

	if ( 'brightcove' === $provider ) {
		$src = sprintf( $pattern, $atts['brightcove_account'], $atts['brightcove_player'], $atts['brightcove_embed'], $id );
	} else {
		$src = sprintf( $pattern, $id );
	}

	return $src;
}

function arve_id_fixes( $id, $provider ) {

	if (
		'liveleak' === $provider &&
		! arve_starts_with( $id, 'i=' ) &&
		! arve_starts_with( $id, 'f=' )
	) {
		$id = 'i=' . $id;
	}

	return $id;
}

function arve_aspect_ratio_fixes( $aspect_ratio, $provider, $mode ) {

	if ( 'dailymotionlist' === $provider ) {
		switch ( $mode ) {
			case 'normal':
			case 'lazyload':
				$aspect_ratio = '640:370';
				break;
		}
	}

	return $aspect_ratio;
}

function arve_add_autoplay_query_arg( $src, $a ) {

	switch ( $a['provider'] ) {
		case 'alugha':
		case 'archiveorg':
		case 'dailymotion':
		case 'dailymotionlist':
		case 'facebook':
		case 'vevo':
		case 'viddler':
		case 'vimeo':
		case 'youtube':
		case 'youtubelist':
			$on  = add_query_arg( 'autoplay', 1, $src );
			$off = add_query_arg( 'autoplay', 0, $src );
			break;
		case 'twitch':
		case 'ustream':
			$on  = add_query_arg( 'autoplay', 'true',  $src );
			$off = add_query_arg( 'autoplay', 'false', $src );
			break;
		case 'livestream':
		case 'wistia':
			$on  = add_query_arg( 'autoPlay', 'true',  $src );
			$off = add_query_arg( 'autoPlay', 'false', $src );
			break;
		case 'metacafe':
			$on  = add_query_arg( 'ap', 1, $src );
			$off = remove_query_arg( 'ap', $src );
			break;
		case 'videojug':
			$on  = add_query_arg( 'ap', 1, $src );
			$off = add_query_arg( 'ap', 0, $src );
			break;
		case 'veoh':
			$on  = add_query_arg( 'videoAutoPlay', 1, $src );
			$off = add_query_arg( 'videoAutoPlay', 0, $src );
			break;
		case 'brightcove':
		case 'snotr':
			$on  = add_query_arg( 'autoplay', 1, $src );
			$off = remove_query_arg( 'autoplay', $src );
			break;
		case 'yahoo':
			$on  = add_query_arg( 'player_autoplay', 'true',  $src );
			$off = add_query_arg( 'player_autoplay', 'false', $src );
			break;
		default:
			# Do nothing for providers that to not support autoplay or fail with parameters
			$on  = $src;
			$off = $src;
			break;
	}

	if ( $a['autoplay'] ) {
		return $on;
	} else {
		return $off;
	}
}

function arve_add_query_args_to_iframe_src( $src, $atts ) {

	$options = arve_get_options();

	$host = $atts['provider'];

	$parameters        = wp_parse_args( preg_replace( '!\s+!', '&', trim( $atts['parameters'] ) ) );
	$option_parameters = array();

	if ( isset( $options['params'][ $host ] ) ) {
		$option_parameters = wp_parse_args( preg_replace( '!\s+!', '&', trim( $options['params'][ $host ] ) ) );
	}

	$parameters = wp_parse_args( $parameters, $option_parameters );

	return add_query_arg( $parameters, $src );
}

function arve_maxwidth_when_aligned( $maxwidth, $align ) {

	$options = arve_get_options();

	if ( $maxwidth < 100 && in_array( $align, array( 'left', 'right', 'center' ), true ) ) {
		$maxwidth = (int) $options['align_maxwidth'];
	}

	return $maxwidth;
}

function arve_get_language_name_from_code( $lang_code ) {
	// This list is based on languages available from localize.drupal.org. See
	// http://localize.drupal.org/issues for information on how to add languages
	// there.
	//
	// The "Left-to-right marker" comments and the enclosed UTF-8 markers are to
	// make otherwise strange looking PHP syntax natural (to not be displayed in
	// right to left). See https://www.drupal.org/node/128866#comment-528929.
	$lang = array(
		'af'          => array( 'Afrikaans', 'Afrikaans' ),
		'am'          => array( 'Amharic', '????????????' ),
		'ar'          => array( 'Arabic', /* Left-to-right marker "???" */ '??????????????', 'RTL' ),
		'ast'         => array( 'Asturian', 'Asturianu' ),
		'az'          => array( 'Azerbaijani', 'Az??rbaycanca' ),
		'be'          => array( 'Belarusian', '????????????????????' ),
		'bg'          => array( 'Bulgarian', '??????????????????' ),
		'bn'          => array( 'Bengali', '???????????????' ),
		'bo'          => array( 'Tibetan', '????????????????????????' ),
		'bs'          => array( 'Bosnian', 'Bosanski' ),
		'ca'          => array( 'Catalan', 'Catal??' ),
		'cs'          => array( 'Czech', '??e??tina' ),
		'cy'          => array( 'Welsh', 'Cymraeg' ),
		'da'          => array( 'Danish', 'Dansk' ),
		'de'          => array( 'German', 'Deutsch' ),
		'dz'          => array( 'Dzongkha', '??????????????????' ),
		'el'          => array( 'Greek', '????????????????' ),
		'en'          => array( 'English', 'English' ),
		'en-x-simple' => array( 'Simple English', 'Simple English' ),
		'eo'          => array( 'Esperanto', 'Esperanto' ),
		'es'          => array( 'Spanish', 'Espa??ol' ),
		'et'          => array( 'Estonian', 'Eesti' ),
		'eu'          => array( 'Basque', 'Euskera' ),
		'fa'          => array( 'Persian, Farsi', /* Left-to-right marker "???" */ '??????????', 'RTL' ),
		'fi'          => array( 'Finnish', 'Suomi' ),
		'fil'         => array( 'Filipino', 'Filipino' ),
		'fo'          => array( 'Faeroese', 'F??royskt' ),
		'fr'          => array( 'French', 'Fran??ais' ),
		'fy'          => array( 'Frisian, Western', 'Frysk' ),
		'ga'          => array( 'Irish', 'Gaeilge' ),
		'gd'          => array( 'Scots Gaelic', 'G??idhlig' ),
		'gl'          => array( 'Galician', 'Galego' ),
		'gsw-berne'   => array( 'Swiss German', 'Schwyzerd??tsch' ),
		'gu'          => array( 'Gujarati', '?????????????????????' ),
		'he'          => array( 'Hebrew', /* Left-to-right marker "???" */ '??????????', 'RTL' ),
		'hi'          => array( 'Hindi', '??????????????????' ),
		'hr'          => array( 'Croatian', 'Hrvatski' ),
		'ht'          => array( 'Haitian Creole', 'Krey??l ayisyen' ),
		'hu'          => array( 'Hungarian', 'Magyar' ),
		'hy'          => array( 'Armenian', '??????????????' ),
		'id'          => array( 'Indonesian', 'Bahasa Indonesia' ),
		'is'          => array( 'Icelandic', '??slenska' ),
		'it'          => array( 'Italian', 'Italiano' ),
		'ja'          => array( 'Japanese', '?????????' ),
		'jv'          => array( 'Javanese', 'Basa Java' ),
		'ka'          => array( 'Georgian', '????????????????????? ?????????' ),
		'kk'          => array( 'Kazakh', '??????????' ),
		'km'          => array( 'Khmer', '???????????????????????????' ),
		'kn'          => array( 'Kannada', '???????????????' ),
		'ko'          => array( 'Korean', '?????????' ),
		'ku'          => array( 'Kurdish', 'Kurd??' ),
		'ky'          => array( 'Kyrgyz', '????????????????' ),
		'lo'          => array( 'Lao', '?????????????????????' ),
		'lt'          => array( 'Lithuanian', 'Lietuvi??' ),
		'lv'          => array( 'Latvian', 'Latvie??u' ),
		'mg'          => array( 'Malagasy', 'Malagasy' ),
		'mk'          => array( 'Macedonian', '????????????????????' ),
		'ml'          => array( 'Malayalam', '??????????????????' ),
		'mn'          => array( 'Mongolian', '????????????' ),
		'mr'          => array( 'Marathi', '???????????????' ),
		'ms'          => array( 'Bahasa Malaysia', '???????? ??????????' ),
		'my'          => array( 'Burmese', '?????????????????????' ),
		'ne'          => array( 'Nepali', '??????????????????' ),
		'nl'          => array( 'Dutch', 'Nederlands' ),
		'nb'          => array( 'Norwegian Bokm??l', 'Norsk, bokm??l' ),
		'nn'          => array( 'Norwegian Nynorsk', 'Norsk, nynorsk' ),
		'oc'          => array( 'Occitan', 'Occitan' ),
		'pa'          => array( 'Punjabi', '??????????????????' ),
		'pl'          => array( 'Polish', 'Polski' ),
		'pt-pt'       => array( 'Portuguese, Portugal', 'Portugu??s, Portugal' ),
		'pt-br'       => array( 'Portuguese, Brazil', 'Portugu??s, Brasil' ),
		'ro'          => array( 'Romanian', 'Rom??n??' ),
		'ru'          => array( 'Russian', '??????????????' ),
		'sco'         => array( 'Scots', 'Scots' ),
		'se'          => array( 'Northern Sami', 'S??mi' ),
		'si'          => array( 'Sinhala', '???????????????' ),
		'sk'          => array( 'Slovak', 'Sloven??ina' ),
		'sl'          => array( 'Slovenian', 'Sloven????ina' ),
		'sq'          => array( 'Albanian', 'Shqip' ),
		'sr'          => array( 'Serbian', '????????????' ),
		'sv'          => array( 'Swedish', 'Svenska' ),
		'sw'          => array( 'Swahili', 'Kiswahili' ),
		'ta'          => array( 'Tamil', '???????????????' ),
		'ta-lk'       => array( 'Tamil, Sri Lanka', '???????????????, ??????????????????' ),
		'te'          => array( 'Telugu', '??????????????????' ),
		'th'          => array( 'Thai', '?????????????????????' ),
		'tr'          => array( 'Turkish', 'T??rk??e' ),
		'tyv'         => array( 'Tuvan', '???????? ??????' ),
		'ug'          => array( 'Uyghur', '??????????' ),
		'uk'          => array( 'Ukrainian', '????????????????????' ),
		'ur'          => array( 'Urdu', /* Left-to-right marker "???" */ '????????', 'RTL' ),
		'vi'          => array( 'Vietnamese', 'Ti???ng Vi???t' ),
		'xx-lolspeak' => array( 'Lolspeak', 'Lolspeak' ),
		'zh-hans'     => array( 'Chinese, Simplified', '????????????' ),
		'zh-hant'     => array( 'Chinese, Traditional', '????????????' ),
	);

	return $lang[ $lang_code ][1];
}
