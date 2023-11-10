<?php


namespace Core\Database\Query;


use Core\Support\QueryTemplateEngineInterface;
use Core\Trait\AttributesHelper;
use Core\Trait\PropertyGetAccessor;
use Core\Trait\RecursiveTemplateRenderer;

/**
 * Class QueryBuilder
 *
 * @package Core\Database
 * @property-read string $query;
 * @property-read array $fields
 * @property-read array $where
 * @property-read array $table
 * @property-read array $values
 * @property-read array $group
 * @property-read array $join
 * @property-read array $set
 * @property-read array $having
 * @property-read array $order
 * @property-read int|bool $limit
 * @property-read int|bool $offset
 * @property-read bool $forUpdate
 */

abstract class BaseQueryTemplateEngine implements QueryTemplateEngineInterface
{

    use AttributesHelper;
    use PropertyGetAccessor;
    use RecursiveTemplateRenderer;

    public const SELECT = 0;
    public const UPDATE = 1;
    public const DELETE = 2;
    public const INSERT = 3;
    public const UNION  = 4;

    protected static array $PART_TEMPLATES = [];
    protected static array $TEMPLATES = [];
    protected static array $TEMPLATE_VALUES = [];

    protected ?int $useTemplate = null;


    protected string $query = '';

    protected array $fields = [];

    protected array $where = [];

    protected array $table = [];

    protected array $values = [];

    protected array $group = [];

    protected array $join = [];

    protected array $set = [];

    protected array $having = [];

    protected array $order = [];

    protected string $union = '';

    protected int|bool $limit = false;

    protected int|bool $offset = false;

    protected bool $forUpdate = false;



    public function __toString(): string
    {
        if ($this->query) return $this->query;
        if (!isset($this->useTemplate)) return '';

        return $this->render(
            static::$TEMPLATES[$this->useTemplate],
            static::$PART_TEMPLATES,
            static::$TEMPLATE_VALUES[$this->useTemplate]($this)
        );

    }

    public function forUpdate(bool $upd = true): static
    {
        $this->query = '';
        $this->forUpdate = $upd;
        return $this;
    }

    public function select(string ...$select): static
    {
        $this->clean();
        $this->useTemplate = self::SELECT;
        $this->fields = $select;
        return $this;
    }

    public function addSelect(string ...$select): static
    {
        $this->query = '';
        $this->useTemplate = self::SELECT;
        $this->fields = array_merge($this->fields, $select);

        return $this;
    }

    public function from(string $table): static
    {
        $this->query = ''; // yeah, that's not ideal, but using __call
        $this->table[] = $table;
        return $this;
    }

    public function join(string $join, string $on, string $type = 'LEFT'): static
    {
        $this->query = '';
        $this->join[] = [$type, $join, $on];
        return $this;
    }

    public function having(string ...$having): static
    {
        $this->query = '';
        $this->having = array_merge($this->having, $having);
        return $this;
    }

    public function group(string ...$group): static
    {
        $this->query = '';
        $this->group = array_merge($this->group, $group);
        return $this;
    }

    public function where(string ...$where): static
    {
        $this->query = '';
        $this->where = array_merge($this->where, $where);
        return $this;
    }

    public function update(string $table): static
    {
        $this->clean();
        $this->table[] = $table;
        $this->useTemplate = static::UPDATE;
        return $this;
    }

    public function values(string ...$values): static
    {
        $this->query = '';
        $this->values = $values;
        return $this;
    }

    public function set(string ...$set): static
    {
        $this->query = '';
        $this->set = $set;
        return $this;
    }

    public function order(string ...$order): static
    {
        $this->query = '';
        $this->order = $order;
        return $this;
    }

    public function limit(int $limit, int $offset = 0): static
    {
        $this->query = '';
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function insert(string $table, array $fields = []): static
    {
        $this->clean();
        $this->table[] = $table;
        $this->fields = $fields;
        $this->useTemplate = self::INSERT;
        return $this;
    }

    public function delete(): static
    {
        $this->clean();
        $this->useTemplate = self::DELETE;
        return $this;
    }


    public function union(string $union): static
    {
        $this->union = $union;
        $this->useTemplate = self::UNION;
        return $this;
    }


    /*
    * @param $name
    * @param $args
    *
    * @return void
    * @throws BadMethodCallException
    *
    * @deprecated
    *
    * Of course, possible scenario of reduced duplications ($this->query = '') can be defined something like this,
    *  but this is being slower.
    *
    * Not so many duplications for transferring to __call
    *

   public function __call($name, $args)
   {
       $_name = "_{$name}";
       if (method_exists($this, $_name)) {
           $this->query = '';
           $this->$$_name(...$args);
       } else {
           throw new BadMethodCallException(static::class, $name);
       }
   }
   */

}
