<?php

namespace Client;

class Query extends Field
{
    public static function query_description_to_object(array $query_description, array &$variables = []): Query
    {
        if (!isset($query_description['name'])) {
            throw new \Exception('Query description have to have a name');
        }
        $alias = $query_description['alias'] ?? '';
        $params = $query_description['params'] ?? [];
        $fields = array_map(
            function ($field) {
                return new Field($field);
            },
            $query_description['fields'] ?? []
        );

        $query = new self($query_description['name'], $params, $fields, $alias, $variables);

        foreach ($query_description['queries'] ?? [] as $nested_query_description) {
            $query->add_child(self::query_description_to_object($nested_query_description));
        }

        return $query;
    }

    public function __construct(string $name, array $params = [], array $fields = [], string $alias = '', array &$variables = [])
    {
        parent::__construct($name, $fields);
        $this->params = $params;
        $this->alias = $alias;
        $this->variables = &$variables;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function alias(): string
    {
        return $this->alias;
    }

    public function data_request(): array
    {
        $query_string = sprintf(
            '%s %s { %s }',
            $this->data_request_query_word,
            Variable::variables_to_request_header_string($this->variables),
            $this->string()
        );

        return [
            'query' => $query_string,
            'variables' => Variable::variables_to_array($this->variables),
        ];
    }

    public function string(): string
    {
        $query_string = parent::string();

        if (!empty($this->params())) {
            $param_string = '(' . $this->params_to_string($this->params()) . ')';
            $query_string = str_replace(' ', $param_string, $query_string);
        }

        if (!empty($this->alias())) {
            $query_string = $this->alias() . ':' . $query_string;
        }

        return $query_string;
    }

    protected $data_request_query_word = 'query';

    private $variables;

    private $params;

    private $alias;

    private function params_to_string(array $params): string
    {
        $result = [];

        foreach ($params as $key => $value) {
            if (!is_string($key)) {
                throw new \Exception('Param key have to be string !');
            }

            if (is_array($value)) {
                if ($this->has_string_keys($value)) {
                    $value_string = sprintf('{ %s }', $this->params_to_string($value));
                } else {
                    $value_string = sprintf('[ %s ]', $this->params_to_string($value));
                }
            } elseif ($value instanceof Variable) {
                $value_string = sprintf('$%s', $value->name());
                $this->variables[] = $value;
            } else {
                $value_string = sprintf('%s', json_encode($value));
            }

            $result[] = $key . ':' . $value_string;
        }

        return implode(' ', $result);
    }

    private function has_string_keys(array $array): bool
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
