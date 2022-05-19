<?php

declare(strict_types=1);

namespace Service\Types;

use GraphQL\Error\Error;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils as GraphQLUtils;
use Safe\Exceptions\JsonException;

class JsonType extends ScalarType
{
		public $name = 'Json';
    public $description = /** @lang Markdown */
        'Arbitrary data encoded in JavaScript Object Notation. See https://www.json.org/.';

    public function serialize($value): string
    {
			$json = json_encode( $value );
			if (!$json) {
				throw new SerializationError('Cannot represent value as json: ' . Utils::printSafe($value));
			}
			return $json;
    }

    public function parseValue($value)
    {
			return $this->decodeJSON($value);
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        if (!property_exists($valueNode, 'value')) {
            throw new Error(
                'Can only parse literals that contain a value, got '.GraphQLUtils::printSafeJson($valueNode)
            );
        }

        return $this->decodeJSON($valueNode->value);
    }

    /**
     * Try to decode a user-given JSON value.
     *
     * @param mixed $value A user given JSON
     *
     * @throws Error
     *
     * @return mixed The decoded value
     */
    protected function decodeJSON($value)
    {
			$json = json_decode( $value, true );
    	if (!$json) {
      	throw new Error('Cannot represent value as json: ' . Utils::printSafe($value));
    	}
			return $json;
    }
}
