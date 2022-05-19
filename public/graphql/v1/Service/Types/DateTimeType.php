<?php
declare(strict_types=1);

namespace Service\Types;

use DateTime;
use DateTimeInterface;
use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class DateTimeType extends ScalarType {
	public $name = 'DateTime';
	public $description = 'The `DateTime` scalar type represents time data, represented as an ISO-8601 encoded UTC date string.';

	public function serialize($value) {
		if (!($value instanceof DateTimeInterface)) {
			throw new Error(sprintf('DateTime cannot represent non DateTime value: %s', Utils::printSafe($value)));
		}
		return $value->format(DateTimeInterface::ATOM);
	}

	public function parseValue($value): ?DateTime {
		$result = self::parseIso8601($value);
		return $result ?: null;
	}

	public function parseLiteral( \GraphQL\Language\AST\Node $valueNode, ?array $variables = null): ?string {
		if (!($valueNode instanceof StringValueNode)) {
			throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
		}
		return $valueNode->value;
	}

	static public function parseIso8601($iso8601String) {
		$formats = [
			'Y-m-d',
			'Y-m-d H:i:s',
			'Y-m-d\TH:i:s',
			'Y-m-d\TH:i:s.u',
			'Y-m-d\TH:i:s.uP',
			'Y-m-d\TH:i:sP',
			DateTimeInterface::ATOM
		];
		$results = [];
		foreach( $formats as $k => $v ) {
			$result = DateTime::createFromFormat( $v, $iso8601String, new \DateTimeZone("UTC") );
			if ( !empty($result) ) { return $result; }
		}
		return false;
	}
}
