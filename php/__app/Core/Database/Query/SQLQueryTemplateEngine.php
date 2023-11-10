<?php


namespace Core\Database\Query;


/**
 * Class SQLQueryTemplateEngine
 *
 * @Notice: that's just for example (not production realisation)
 * more real-code will consist of write "modules" for every part (select/join/where/condition... etc)
 * and maybe using some "syntax" class (compiling by something like join->build() / (string) $queryPart)
 *
 * but creating separate objects for each part of the query also does not seem to be the right approach(can be performance issues)..
 * also there is option with usage of traits for pre-compile different parts of query
 *
 * @package Core\Database
 */

class SQLQueryTemplateEngine extends BaseQueryTemplateEngine
{

    protected static array $PART_TEMPLATES = [
        '{select}'    => "SELECT %s\n",
        '{from}'      => "FROM %s\n",
        '{where}'     => "WHERE %s\n",
        '{join}'      => "%s JOIN %s ON %s\n",
        '{group}'     => "GROUP BY %s\n",
        '{order}'     => "ORDER BY %s\n",
        '{having}'    => "HAVING %s\n",
        '{limit}'     => "LIMIT %s OFFSET %s\n",
        '{update}'    => "UPDATE %s\n",
        '{set}'       => "SET %s\n",
        '{values}'    => "VALUES (%s)\n",
        '{insert}'    => "INSERT INTO %s (%s)\n",
        '{delete}'    => "DELETE \n",
        '{forUpdate}' => "FOR UPDATE \n",
        '{union}'     => "%s UNION %s\n"
    ];

    /**
     * Preprocessing parts of the request
     * template name(part) =>
     *  ['valueOrProperty', [ function/method, [arguments] ], [ function 2/method, [arguments] ]... ]
     *
     * After all, there will be a breakdown into entities later
     *
     * Now the algorithm is as follows:
     * 1) we determine the type of query
     * 2) we run through its component parts with preprocessing (you can set custom functions)
     * 3) glue
     *
     * p.s.: yes, it is more difficult to understand & edit this class according to kiss/dry principles
     *
     * todo: state control for modified parts?
     */

    protected static array $TEMPLATES =
        [
            self::SELECT => "{select}{from}{join}{where}{group}{having}{order}{limit}{forUpdate}",
            self::UPDATE => "{update}{set}{where}",
            self::DELETE => "{delete}{from}{where}",
            self::INSERT => "{insert}{values}",
            self::UNION  => "{union}",
        ];



    public static function __onAutoload(): void
    {

        static::$TEMPLATE_VALUES =
            [
                self::SELECT => fn(self $obj) => [
                    '{select}'      => implode(', ', $obj->fields),
                    '{from}'        => implode(', ', $obj->table),
                    '{join}'        => $obj->join,
                    '{where}'       => implode(', ', $obj->where),
                    '{group}'       => implode(', ', $obj->group),
                    '{having}'      => implode(', ', $obj->having),
                    '{order}'       => implode(', ', $obj->order),
                    '{limit}'       => $obj->limit !== false ? [$obj->limit, (int)$obj->offset] : false,
                    '{forUpdate}'   => $obj->forUpdate,
                ],

                self::UPDATE => fn(self $obj) => [
                    '{update}'      => implode(', ', $obj->table),
                    '{set}'         => implode(', ', $obj->set),
                    '{where}'       => implode(' ', $obj->where),
                ],

                self::DELETE => fn(self $obj) => [
                    '{delete}'      => true,
                    '{from}'        => implode(', ', $obj->table),
                    '{where}'       => implode(' ', $obj->where),
                ],

                self::INSERT => fn(self $obj) => [
                    '{insert}'      => [implode(', ', $obj->table), implode(', ', $obj->fields)],
                    '{values}'      => implode(', ', $obj->values),
                ],

                self::UNION => fn(self $obj) => [
                    '{union}'       => [$obj->__toString(), $obj->union]
                ]

            ];
    }
}