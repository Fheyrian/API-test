<?php
// require 'vendor/autoload.php';


declare(strict_types=1);


use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

require_once realpath(__DIR__ . '/..') . "/vendor/autoload.php";
// require 'vendor/autoload.php';
// require_once __DIR__ . "\html_tag_helper.php";

// Setup some additional prefixes for DBpedia
\EasyRdf\RdfNamespace::set('dbc', 'http://dbpedia.org/resource/Category:');
\EasyRdf\RdfNamespace::set('dbpedia', 'http://dbpedia.org/resource/');
\EasyRdf\RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
\EasyRdf\RdfNamespace::set('dbp', 'http://dbpedia.org/property/');

$sparql = new \EasyRdf\Sparql\Client('http://dbpedia.org/sparql');

$result = $sparql->query(
    'SELECT * WHERE {'.
    '  ?country rdf:type dbo:Country .'.
    '  ?country rdfs:label ?label .'.
    '  ?country dct:subject dbc:Member_states_of_the_United_Nations .'.
    '  FILTER ( lang(?label) = "en" )'.
    '} ORDER BY ?label'
);

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    $app->get('/getCountry', function (Request $request, Response $response) {
        $sparql = new \EasyRdf\Sparql\Client('http://dbpedia.org/sparql');
        $response = $sparql->query(
            'SELECT * WHERE {'.
            '  ?country rdf:type dbo:Country .'.
            '  ?country rdfs:label ?label .'.
            '  ?country dct:subject dbc:Member_states_of_the_United_Nations .'.
            '  FILTER ( lang(?label) = "en" )'.
            '} ORDER BY ?label'
        );
        // $response->getBody()->write('Hello world!');
        return $response;
    });
};
