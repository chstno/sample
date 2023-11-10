<?php


namespace Core\Database\Query;


use Core\Support\ComparisonExpressionInterface;
use Core\Support\ExpressionInterface;
use Core\Support\FunctionExpressionInterface;
use Core\Trait\MethodsAliases;

/**
 * todo: think about separation in future versions
 *
 * (object creation on every event - get flexibility, but can make impact on performance)
 */
class BaseExpression implements ExpressionInterface, ComparisonExpressionInterface, FunctionExpressionInterface
{

    use MethodsAliases;

    public const AND            = ' AND ';
    public const OR             = ' OR ';
    public const LIKE           = ' LIKE ';
    public const NOT            = ' NOT ';
    public const IN             = ' IN ';

    public const EQUAL          = ' = ';
    public const NOT_EQUAL      = ' <> ';
    public const LOWER          = ' < ';
    public const BIGGER         = ' > ';
    public const LOWER_EQUAL    = ' <= ';
    public const BIGGER_EQUAL   = ' >= ';
    public const BETWEEN        = ' BETWEEN ';

    public const EXISTS         = ' EXISTS ';
    public const IFNULL         = ' IFNULL ';
    public const IF             = ' IF ';
    public const CASE           = ' CASE ';
    public const WHEN           = ' WHEN ';
    public const THEN           = ' THEN ';
    public const COMMA          = ' , ';

    public const COUNT          = ' COUNT ';
    public const AVG            = ' AVG ';
    public const SUM            = ' SUM ';

    public const SUB_START      = ' ( ';
    public const SUB_END        = ' ) ';

    protected string $expr = '';

    protected array $aliases = [
        '='  => 'equal',
        '<'  => 'lower',
        '>'  => 'bigger',
        '<=' => 'lowerOrEqual',
        '>=' => 'biggerOrEqual',
        '!=' => 'notEqual',
        '<>' => 'notEqual',
        '&&' => 'and',
        '&'  => 'and',
        '||' => 'or',
        ','  => 'comma'
    ];

    public function __toString(): string
    {
        return $this->expr;
    }

    protected function addComparison(?string $var, ?string $value, string $operand): static
    {
        if (!$var) {
            $this->expr .= $operand;
        } else {
            $this->expr .= "{$var} {$operand} {$value}";
        }

        return $this;
    }

    public function equal(?string $var, ?string $value): static
    {
        return $this->addComparison($var, $value, static::EQUAL);
    }

    public function notEqual(?string $var, ?string $value): static
    {
        return $this->addComparison($var, $value, static::NOT_EQUAL);
    }

    public function biggerOrEqual(?string $var, ?string $value): static
    {
        return $this->addComparison($var, $value, static::BIGGER_EQUAL);
    }

    public function bigger(?string $var, ?string $value): static
    {
        return $this->addComparison($var, $value, static::BIGGER);
    }

    public function lower(?string $var, ?string $value): static
    {
        return $this->addComparison($var, $value, static::LOWER);
    }

    public function lowerOrEqual(?string $var, ?string $value): static
    {
        return $this->addComparison($var, $value, static::LOWER_EQUAL);
    }

    public function like(?string $var, ?string $value): static
    {
        return $this->addComparison($var, $value, static::LIKE);
    }

    public function and(string ...$expr): static
    {
        return $this->composition(static::AND, ...$expr);
    }

    public function or(string ...$expr): static
    {
       return $this->composition(static::OR, ...$expr);
    }

    public function not(): static
    {
        $this->expr .= static::NOT;
        return $this;
    }

    public function exists() : static
    {
        $this->expr .= static::EXISTS;
        return $this;
    }

    public function ifnull(string $expr, string $return): static
    {
        $ifnull = static::IFNULL;
        $this->expr .= "{$ifnull}({$expr}, {$return})";
        return $this;
    }

    public function if(string $if, string $then, string $otherwise): static
    {
        $_if = static::IF;
        $this->expr .= "{$_if}({$if}, {$then}, {$otherwise})";
        return $this;
    }

    public function in(string|array $in): static
    {
        $_in = static::IN;

        if (is_array($in))
            $in = implode(',', $in);

        $this->expr .= "{$_in}($in)";
        return $this;
    }

    public function literal(mixed $value): static
    {
        $this->expr .= is_int($value) ? $value : "'$value'";
        return $this;
    }

    public function parameter(string $name): static
    {
        $this->expr .= ":{$name}";
        return $this;
    }

    public function field(string $name, ?string $table): static
    {
        $this->expr .= $table ? "{$table}.{$name}" : "$name";
        return $this;
    }

    public function table(string $name): static
    {
        $this->expr .= $name;
        return $this;
    }

    public function alias(string $alias): static
    {
        $this->expr .= " as {$alias}";
        return $this;
    }

    public function comma(): static
    {
        $this->expr .= static::COMMA;
        return $this;
    }

    public function case(string $case, string $when, string $then): static
    {
        $_case = static::CASE;
        $_when = static::WHEN;
        $_then = static::THEN;
        // "when" - is other expression (equal/bigger etc..)
        $this->expr .= "{$_case}{$case} {$_when}{$when} {$_then}{$then}";
        return $this;
    }

    public function between(string $var, string|int $val1, string|int $val2): static
    {
        $between = static::BETWEEN;
        $and = static::AND;
        $this->expr .= "{$var} {$between} $val1 {$and} $val2";
        return $this;
    }

    public function count(string $var): static
    {
        return $this->func(static::COUNT, $var);
    }

    public function avg(string $var): static
    {
        return $this->func(static::AVG, $var);
    }

    public function sum(string $var): static
    {
        return $this->func(static::SUM, $var);
    }

    public function func(string $name, string ...$args): static
    {
        $args = implode(', ', $args);
        $this->expr .= "{$name}($args)";
        return $this;
    }

    public function composition(string $operator, string ...$expr): static
    {
        $this->expr .= $operator;

        if ($expr)
            $this->expr .= implode($operator, $expr);

        return $this;
    }

    public function sub(string|ExpressionInterface $expression): static
    {
        $this->expr .= static::SUB_START . $expression . static::SUB_END;
        return $this;
    }
}