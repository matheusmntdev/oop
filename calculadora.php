<?php

class Calculadora { 
    public function somar(...$numeros) {
        if (empty($numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        return array_sum($numeros);
    }
 
    public function subtrair(...$numeros) {
        if (empty($numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        $resultado = $numeros[0];
        for ($i = 1; $i < count($numeros); $i++) {
            $resultado -= $numeros[$i];
        }
        return $resultado;
    }
 
    public function multiplicar(...$numeros) {
        if (empty($numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        $resultado = 1;
        foreach ($numeros as $numero) {
            $resultado *= $numero;
        }
        return $resultado;
    }
 
    public function dividir(...$numeros) {
        if (empty($numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        $resultado = $numeros[0];
        for ($i = 1; $i < count($numeros); $i++) {
            if ($numeros[$i] == 0) {
                return "Erro: Divisão por zero não é permitida!";
            }
            $resultado /= $numeros[$i];
        }
        return $resultado;
    }
} 

function exibirMenu() {
    while (true) {
        echo "\n=== Calculadora ===\n";
        echo "1. Somar\n";
        echo "2. Subtrair\n";
        echo "3. Multiplicar\n";
        echo "4. Dividir\n";
        echo "5. Sair\n";
        echo "Escolha uma opção (1-5): ";
        $opcao = trim(fgets(STDIN)); 

        if (is_numeric($opcao) && $opcao >= 1 && $opcao <= 5) {
            return (int)$opcao;
        } else {
            echo "Erro: Opção inválida! Por favor, digite um número entre 1 e 5.\n";
        }
    }
}
 
function capturarNumeros() {
    while (true) {
        echo "Digite os números separados por espaço (ex: 10 5 3): ";
        $entrada = trim(fgets(STDIN));
        
        if (empty($entrada)) {
            echo "Erro: Nenhuma entrada fornecida! Tente novamente.\n";
            continue;
        }

        $numeros = explode(' ', $entrada);
        $numerosValidos = [];
        $entradaValida = true;

        foreach ($numeros as $num) {
            $num = trim($num);
            if ($num === '' || !is_numeric($num)) {
                echo "Erro: '$num' não é um número válido! Tente novamente.\n";
                $entradaValida = false;
                break;
            }
            $numerosValidos[] = floatval($num);
        }

        if ($entradaValida && count($numerosValidos) > 0) {
            return $numerosValidos;
        }
    }
}
 
$calc = new Calculadora();
 
while (true) {
    $opcao = exibirMenu();

    if ($opcao == 5) {
        echo "Saindo da calculadora. Até mais!\n";
        break;
    }

    $numeros = capturarNumeros();

    switch ($opcao) {
        case 1:
            $resultado = $calc->somar(...$numeros);
            echo "Resultado da soma: $resultado\n";
            break;
        case 2:
            $resultado = $calc->subtrair(...$numeros);
            echo "Resultado da subtração: $resultado\n";
            break;
        case 3:
            $resultado = $calc->multiplicar(...$numeros);
            echo "Resultado da multiplicação: $resultado\n";
            break;
        case 4:
            $resultado = $calc->dividir(...$numeros);
            echo "Resultado da divisão: $resultado\n";
            break;
    }
}

?>