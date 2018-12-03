<?php
namespace Dreamwhite\Assortment;
require "includes.php";


$client = \Softonic\GraphQL\ClientBuilder::build('https://backend.dreamwhite.ru/graphql');

$query = <<<'QUERY'
query {
    products {
        name
        msid
        description
        article {
            description
        }
    }
}
QUERY;


$response = $client->query($query);
var_dump($response);