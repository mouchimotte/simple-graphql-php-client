<?php

namespace Client;

class Mutation extends Query
{
    public static function mutation_description_to_object(array $mutation_description, array &$variables = []): Mutation
    {
        if (!isset($mutation_description['name'])) {
            throw new \Exception('Mutation description have to have a name');
        }
        $alias = $mutation_description['alias'] ?? '';
        $params = $mutation_description['params'] ?? [];
        $fields = array_map(
            function ($field) {
                return new Field($field);
            },
            $mutation_description['fields'] ?? []
        );

        $mutation = new self($mutation_description['name'], $params, $fields, $alias, $variables);

        foreach ($mutation_description['queries'] ?? [] as $nested_query_description) {
            $mutation->add_child(Query::query_description_to_object($nested_query_description));
        }

        return $mutation;
    }

    protected $data_request_query_word = 'mutation';
}
