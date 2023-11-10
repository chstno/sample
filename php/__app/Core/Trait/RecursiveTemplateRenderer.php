<?php

namespace Core\Trait;

/**
 *
 */

trait RecursiveTemplateRenderer
{

    /**
     *
     * Used as a basic recursive template renderer.
     *
     *
     * @param array $templateStructure
     * @param array $values
     *
     * @return string
     *
     *
     *
     * render(['some {joins}', '{joins}' => "%s JOIN %s ON %s\n {test}\n", '{test}' => '%s'], ['{joins}' => [[1,2,3], [2,3,4], [5,6,7]], '{test}' => [1,2,3]])
     *
     * will produce result:
     * "some 1 JOIN 2 ON 3
     * 123
     * 2 JOIN 3 ON 4
     * 123
     * 5 JOIN 6 ON 7
     * 123
     * "
     *
     */

    public function render(string $mainTemplate, array $templateStructure, array $values): string
    {

        foreach ($templateStructure as $templateName => $template) {

            $value = $values[$templateName] ?? null;

            if (!$value && $value !== 0) {
                $templateStructure[$templateName] = ''; // empty that part (we don't want to include)
                continue;
            }

            $valueReplacesCnt = substr_count($template, '%s');

            if ($valueReplacesCnt > 1) {

                if (is_array(current($value))) {

                    $templateStructure[$templateName] = '';

                    foreach ($value as $valuesRow) {
                        $templateStructure[$templateName] .= sprintf($template, ...$valuesRow);
                    }

                } else {
                    $templateStructure[$templateName] = sprintf($template, ...$value);
                }

            } elseif ($valueReplacesCnt === 1) {

                if (!is_array($value))
                    $templateStructure[$templateName] = sprintf($template, $value);
                else {
                    $templateStructure[$templateName] = '';
                    foreach ($value as $subValue) {
                        $templateStructure[$templateName] .= sprintf($template, $subValue);
                    }
                }
            }
        }

        return str_replace(array_keys($templateStructure), array_values($templateStructure), $mainTemplate);
    }

    /*
    public function process(array|string $template, array $values): array
    {
        if (is_array($template)) {

            foreach ($template as $key => $sub) {

                if (is_array($sub))
                    $template[$key] = process($sub, current($values));

                if (is_int($key)) {

                } else {

                }

                next($values);
            }

        } else {

        }
    }
    */

}