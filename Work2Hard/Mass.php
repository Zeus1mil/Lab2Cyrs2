<?php

function isOperator(string $c): bool
{
    return ($c === '+' || $c === '-' || $c === '*' || $c === '/');
}

function getOperatorPriority(string $op): int
{
    if ($op === '+' || $op === '-') {
        return 1;
    }
    if ($op === '*' || $op === '/') {
        return 2;
    }
    return 0;
}

function calculation(float $operand1, float $operand2, string $op): float|string
{
    if (!preg_match('/^[\d\s\(\)\+\-\*\/\.]+$/', $operand1 . $op . $operand2)) {
        return "Ошибка! Введены некорректные символы.";
    }

    switch ($op) {
        case '+':
            return $operand1 + $operand2;
        case '-':
            return $operand1 - $operand2;
        case '*':
            if ($operand2 === 0) {
                return "Ошибка! Деление на ноль!";
            }
            return $operand1 * $operand2;
        case '/':
            if ($operand2 === 0) {
                return "Ошибка! Деление на ноль!";
            }
            return $operand1 / $operand2;
        default:
            return 0;
    }
}

function calculateExample(string $example): float|string
{
    $opStack = array();
    $numStack = array();
    $inParentheses = false;

    for ($i = 0; $i < strlen($example); $i++) {
        $c = $example[$i];

        if ($c === '(') {
            array_push($opStack, $c);
        } elseif ($c === ')') {
            while (!empty($opStack) && end($opStack) !== '(') {
                $op = array_pop($opStack);
                $operand2 = array_pop($numStack);
                $operand1 = array_pop($numStack);
                array_push($numStack, calculation($operand1, $operand2, $op));
            }
            array_pop($opStack);
        } elseif (is_numeric($c) || $c === '.') {
            $numStr = $c;
            while ($i + 1 < strlen($example) && (is_numeric($example[$i + 1]) || $example[$i + 1] === '.')) {
                $numStr .= $example[$i + 1];
                $i++;
            }
            array_push($numStack, floatval($numStr));
        } elseif (isOperator($c)) {
            while (!empty($opStack) && getOperatorPriority(end($opStack)) >= getOperatorPriority($c)) {
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
