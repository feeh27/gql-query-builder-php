<?php

namespace GQLQueryBuilder;

class DefaultMutationAdapter
{
    private $variables;
    private $fields;
    private $operation;

    public function __construct(array $options)
    {

        if (array_is_list($options)) {
            $this->variables = Utils::resolveVariables($options);
        } else {
            if (isset($options['variables'])) {
                $this->variables = $options['variables'];
            }

            $this->fields = $options['fields'] ?? [];
            $this->operation = $options['operation'];
        }
    }

    public function mutationBuilder()
    {
        return $this->operationWrapperTemplate(
            $this->operationTemplate($this->variables)
        );
    }

    private function operationWrapperTemplate(string $content)
    {
        $query = "mutation";
        $query .= $this->queryDataArgumentAndTypeMap() . " { " . $content . " }";

        return [
            "query" => $query,
            "variables" => Utils::queryVariablesMap($this->variables, $this->fields),
        ];
    }

    private function  queryDataArgumentAndTypeMap(): string
    {
        $variablesUsed = $this->variables ?? [];


        if ($this->fields && is_array($this->fields)) {
            $variablesUsed = array_merge($variablesUsed, Utils::getNestedVariables($this->fields));
        }
        if (count($variablesUsed) > 0) {

            $s = [];
            foreach ($variablesUsed as $key => $value) {
                $s[] = '$' . $key . ': ' . Utils::queryDataType($value);
            }
            return '(' . implode(', ', $s) . ')';
        } else {
            return '';
        }
    }
    
    private function operationTemplate(?array $variables = null)
    {
        $operation = is_string($this->operation) ? $this->operation : $this->operation['alias'] . ': ' . $this->operation['name'];

        return $operation . ($variables ? Utils::queryDataNameAndArgumentMap($variables) : '') . ($this->fields && count($this->fields) > 0 ? '{ ' . Utils::queryFieldsMap($this->fields) . ' }' : '');
    }
}
