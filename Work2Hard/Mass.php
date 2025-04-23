<?php

function isOperator($c)
{
    return ($c == '+' || $c == '-' || $c == '*' || $c == '/');
}

function getOperaroePriority($op)
{
    if ($op === '+' || $op === '-') {
        return 1;
    }
    if ($op === '*' || $op === '/') {
        return 2;
    }
    return 0;
}

function calculation($operand1, $operand2, $op)
{
    if (!preg_match('/^[\d\s\(\)\+\-\*\/\.]+$/', $operand1 . $op . $operand2)) {
        return "Ошибка! Введены некорректные символы.";
    }

    if ($op === '+') {
        return $operand1 + $operand2;
    } else if ($op === '-') {
        return $operand1 - $operand2;
    } else if ($op === '*') {
        if ($operand2 === 0 || $operand1 === 0) {
            return "Ошибка! Деление на ноль!" . PHP_EOL;
        } else {
            return $operand1 * $operand2;
        }
    } else if ($op === '/') {
        if ($operand1 === 0 || $operand2 === 0) {
            return "Ошибка! Деление на ноль!" . PHP_EOL;
        } else {
            return $operand1 / $operand2;
        }
    }
    return 0;
}

function calculateExample(&$example)
{

    $opStack = array();
    $numStack = array();
    for ($i = 0; $i < strlen($example); $i++) {
        $c = $example[$i];
        if ($c === '(') {
            $inParentheses = true;
        } elseif ($c === ')') {
            $inParentheses = false;
            if (empty($numStack)) {
                return "Ошибка! В скобках нету выражения!";
            }
        } else if (is_numeric($c) || $c === '.') {
            $numStr = $c;
            while ($i + 1 < strlen($example) && (is_numeric($example[$i + 1]) || $example[$i + 1] === '.')) {
                $numStr .= $example[$i + 1];
                $i++;
            }

            array_push($numStack, floatval($numStr));
        } else if ($c === '(') {
            array_push($opStack, '(');
        } else if ($c === ')') {
            while (end($opStack) != '(') {
                $op = array_pop($opStack);

                $operand2 = array_pop($numStack);
                $operand1 = array_pop($numStack);

                array_push($numStack, calculation($operand1, $operand2, $op));
            }
            array_pop($opStack);
        } else if (isOperator($c)) {
            while (!empty($opStack) && getOperaroePriority(end($opStack)) >= getOperaroePriority($c)) {
                $op = array_pop($opStack);

                $operand2 = array_pop($numStack);
                $operand1 = array_pop($numStack);

                array_push($numStack, calculation($operand1, $operand2, $op));
            }
            array_push($opStack, $c);
        }
    }

    while (!empty($opStack)) {
        $op = array_pop($opStack);

        $operand2 = array_pop($numStack);
        $operand1 = array_pop($numStack);

        array_push($numStack, calculation($operand1, $operand2, $op));
    }
    return end($numStack);
}

echo "Введите пример: ";
$example = readline();

$verification = "0123456789()+-*/";


for ($i = 0; $i < strlen($example); $i++) {
    if (strpos($verification, $example[$i]) === false) {
        echo "Ошибка! В примере содержиться посторонние символы!" . PHP_EOL;
        exit;
    }
}

$result = calculateExample($example);
echo "Ответ: " . $result . PHP_EOL;
