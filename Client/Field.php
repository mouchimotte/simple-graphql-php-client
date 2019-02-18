<?php

namespace Client;

class Field
{
    public function __construct(string $name, array $children = [])
    {
        $this->name = $name;
        $this->children = $children;
    }

    public function children(): array
    {
        return $this->children;
    }

    public function add_child(Field $field): Field
    {
        $this->children[] = $field;

        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function string(): string
    {
        $field_string = '';

        if ($this->children()) {
            $field_string .= '{';
            foreach ($this->children() as $child) {
                $field_string .= sprintf('%s', $child->string());
                $field_string .= PHP_EOL;
            }
            $field_string .= '}';
        }

        return sprintf('%s %s', $this->name(), $field_string);
    }

    private $children;

    private $name;

    public function __toString()
    {
        return $this->string();
    }
}
