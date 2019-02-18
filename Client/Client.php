<?php

namespace Client;

class Client
{
    public static function queries_description_to_request(array $queries_description, array &$variables = []): array
    {
        $queries = array_map(
            function (array $query_description) use (&$variables) {
                return Query::query_description_to_object($query_description, $variables)->string();
            },
            $queries_description
        );

        $query_string = sprintf(
            'query %s { %s }',
            Variable::variables_to_request_header_string($variables),
            implode("\n", $queries)
        );

        return [
            'query' => $query_string,
            'variables' => Variable::variables_to_array($variables),
        ];
    }

    public static function mutations_description_to_request(array $mutations_description, array &$variables = []): array
    {
        $mutations = array_map(
            function (array $mutation_description) use (&$variables) {
                return Mutation::mutation_description_to_object($mutation_description, $variables)->string();
            },
            $mutations_description
        );

        $query_string = sprintf(
            'mutation %s { %s }',
            Variable::variables_to_request_header_string($variables),
            implode("\n", $mutations)
        );

        return [
            'query' => $query_string,
            'variables' => Variable::variables_to_array($variables),
        ];
    }
}
