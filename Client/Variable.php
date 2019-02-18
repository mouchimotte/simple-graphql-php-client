<?php

namespace Client;

class Variable
{
    public static function variables_to_array($variables)
    {
        $ret = [];
        foreach ($variables as $variable) {
            if (!($variable instanceof Variable)) {
                throw new \Exception('Variable have to be an instance of Variable');
            }

            $ret[$variable->name()] = $variable->value();
        }

        return $ret;
    }

    public static function variables_to_request_header_string(array $variables): string
    {
        if (!count($variables)) {
            return '';
        }

        $result = [];
        foreach ($variables as $variable) {
            if (!($variable instanceof Variable)) {
                throw new \Exception('Variable have to be an instance of Variable');
            }

            $result[] = '$' . $variable->name() . ':' . $variable->type();
        }

        return 'Header(' . implode(' ', $result) . ')';
    }

    public function __construct(string $name, $value, string $type = 'String')
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }

    public function value()
    {
        return $this->value;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): string
    {
        return $this->type;
    }

    private $name;

    private $value;

    private $type;
}
