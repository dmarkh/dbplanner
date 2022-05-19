<?php

function gqlFieldResolver( $objectValue, $args, $context, \GraphQL\Type\Definition\ResolveInfo $info ) {

	$fieldName = $info->fieldName;

	// default reply on empty input
	if ( $fieldName == 'hello' ) {
		return 'GraphQL endpoint is ready. Use GraphiQL to browse API.';
	}

	$level = count( $info->path );

	if ( $level == 1 ) {
		switch( $fieldName ) {
			case 'getStats':
				return \Service\DataSource::getStats();
			case 'getPoll':
				return \Service\DataSource::getPoll( $args['pid'] );
			case 'setPoll':
				error_log( print_r($args,true), 0 );
				return \Service\DataSource::setPoll( $args['poll']['title'], $args['poll']['cid'], $args['poll']['cname'],
					$args['poll']['notes'], $args['poll']['location'], $args['poll']['videolink'], $args['poll']['timezone'], $args['poll']['dates'] );
			case 'setVote':
				return \Service\DataSource::setVote( $args['vote']['pid'], $args['vote']['uid'],
					$args['vote']['uname'], $args['vote']['data'] );
		}
	} else if ( $level == 2 ) {
		if ( $fieldName == 'votes' ) {
			return \Service\DataSource::getVotes( $objectValue['pid'] );
		}
	}

	$property  = null;

	if ( is_array( $objectValue ) || $objectValue instanceof \ArrayAccess ) {
		if ( isset( $objectValue[$fieldName] ) ) {
			$property = $objectValue[$fieldName];
		}
	} elseif ( is_object( $objectValue ) ) {
		if ( isset( $objectValue->{$fieldName} ) ) {
			$property = $objectValue->{$fieldName};
		}
	}

	return $property instanceof Closure
		? $property($objectValue, $args, $context, $info)
		: $property;
}
