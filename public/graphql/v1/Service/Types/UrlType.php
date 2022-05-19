<?php

declare(strict_types=1);

namespace Service\Types;

use GraphQL\Error\Error;
use GraphQL\Error\SerializationError;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

use function filter_var;
use function is_string;

use const FILTER_VALIDATE_URL;


class UrlType extends ScalarType {

	public $name = 'Url';
	public $description = 'The `Url` scalar type represents urls encoded as string';

	public function serialize($value): string {
		if (! $this->isUrl($value)) {
			throw new SerializationError('Cannot represent value as URL: ' . Utils::printSafe($value));
		}
		return $value;
	}

	public function parseValue($value): string {
		if (! $this->isUrl($value)) {
			throw new Error('Cannot represent value as URL: ' . Utils::printSafe($value));
		}
		return $value;
	}

	public function parseLiteral(Node $valueNode, ?array $variables = null): string {
		if (! ($valueNode instanceof StringValueNode)) {
			throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, [$valueNode]);
		}
		$value = $valueNode->value;
		if (! $this->isUrl($value)) {
			throw new Error('Query error: Not a valid URL', [$valueNode]);
		}
		return $value;
	}

	private function isUrl($value): bool {
		return is_string($value)
			&& filter_var($value, FILTER_VALIDATE_URL);
	}
}
