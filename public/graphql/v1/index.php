<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/polyfills.php';

ini_set('memory_limit','256M');
ini_set('display_errors', 0);

use GraphQL\Language\AST\ScalarTypeDefinitionNode;
use GraphQL\Utils\BuildSchema;
use GraphQL\Error\FormattedError;
use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;

use Service\AppContext;
use Service\DataSource;
use Service\FieldResolver;

require_once( __DIR__.'/Service/gqlFieldResolver.php' );
require_once( __DIR__.'/gqschema.php' );

$debug = DebugFlag::NONE;
if (!empty($_GET['debug'])) {
	set_error_handler(function($severity, $message, $file, $line) use (&$phpErrors) {
		throw new ErrorException($message, 0, $severity, $file, $line);
	});
	$debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
}

try {

	// Parse incoming query and variables
	if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
		$raw = file_get_contents('php://input') ?: '';
		$data = json_decode($raw, true) ?: [];
	} else {
		$data = $_REQUEST;
	}

	$data += ['query' => null, 'variables' => null];
	if ( null === $data['query'] ) {
		$data['query'] = '{hello}';
	}

	// support for custom scalar types like Email and Url
	$typeConfigDecorator = function($typeConfig, $typeDefinitionNode) {
    $astNode = $typeConfig['astNode'] ?? null;
    if ($astNode instanceof ScalarTypeDefinitionNode) {
        $scalarHandlerClass = "Service\\Types\\{$typeConfig['name']}Type";
        if (!class_exists($scalarHandlerClass, true)) {
            throw new MyException("Custom scalar {$typeConfig['name']} must have corresponding type class $scalarHandlerClass");
        }
        $handler = new $scalarHandlerClass();
        $typeConfig = array_merge($typeConfig, [
            'serialize' => [$handler, 'serialize'],
            'parseValue' => [$handler, 'parseValue'],
            'parseLiteral' => [$handler, 'parseLiteral'],
        ]);
    }
    return $typeConfig;
	};

	$schema = BuildSchema::build( $gqschema, $typeConfigDecorator );

	$rootValue = [ 'prefix' => '' ];
	$appContext = new AppContext();

	// import variables into context
	$appContext->variables = (array)$data['variables'];

	// process the request
	$result = GraphQL::executeQuery(
		$schema,										// Schema
		$data['query'],							// queryString
		$rootValue,									// rootValue
		$appContext,								// appContext
		(array) $data['variables'], // variables
		NULL,												// operationName
		'gqlFieldResolver',					// fieldResolver
		NULL												// validationRules
	);

	$output = $result->toArray($debug);
	$httpStatus = 200;

} catch (\Exception $error) {
	$httpStatus = 500;
	$output['errors'] = [
		FormattedError::createFromException($error, $debug)
	];
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json; charset=UTF-8', true, $httpStatus);
echo json_encode($output);
