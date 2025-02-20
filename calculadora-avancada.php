<?php

abstract class Operacao {
    protected $nome;
    protected $numeros;

    public function __construct(array $numeros) {
        $this->numeros = $numeros;
    }

    abstract public function calcular(): float|string;

    public function getNome(): string {
        return $this->nome;
    }

    public function getNumeros(): array {
        return $this->numeros;
    }
}
 
class Soma extends Operacao {
    protected $nome = "Soma";

    public function calcular(): float|string {
        if (empty($this->numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        return array_sum($this->numeros);
    }
}
 
class Subtracao extends Operacao {
    protected $nome = "Subtração";

    public function calcular(): float|string {
        if (empty($this->numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        $resultado = $this->numeros[0];
        for ($i = 1; $i < count($this->numeros); $i++) {
            $resultado -= $this->numeros[$i];
        }
        return $resultado;
    }
}
 
class Multiplicacao extends Operacao {
    protected $nome = "Multiplicação";

    public function calcular(): float|string {
        if (empty($this->numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        $resultado = 1;
        foreach ($this->numeros as $numero) {
            $resultado *= $numero;
        }
        return $resultado;
    }
}
 
class Divisao extends Operacao {
    protected $nome = "Divisão";

    public function calcular(): float|string {
        if (empty($this->numeros)) {
            return "Erro: Nenhum número fornecido!";
        }
        $resultado = $this->numeros[0];
        for ($i = 1; $i < count($this->numeros); $i++) {
            if ($this->numeros[$i] == 0) {
                return "Erro: Divisão por zero não é permitida!";
            }
            $resultado /= $this->numeros[$i];
        }
        return $resultado;
    }
}
 
class Calculadora {
    private $historico = [];
    private $precisaoDecimal = 2;
    private $limiteNumeros = 10;

    public function executarOperacao(Operacao $operacao): void {
        try {
            $resultado = $operacao->calcular();
            if (is_numeric($resultado)) {
                $resultado = round($resultado, $this->precisaoDecimal);
            }
            $this->historico[] = [
                'operacao' => $operacao->getNome(),
                'numeros' => $operacao->getNumeros(),
                'resultado' => $resultado,
                'data' => date('Y-m-d H:i:s')
            ];
            echo "Resultado da {$operacao->getNome()}: $resultado\n";
        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage() . "\n";
        }
    }

    public function setPrecisaoDecimal(int $precisao): void {
        if ($precisao < 0 || $precisao > 10) {
            throw new Exception("Precisão deve estar entre 0 e 10!");
        }
        $this->precisaoDecimal = $precisao;
        echo "Precisão decimal ajustada para $precisao casas.\n";
    }

    public function setLimiteNumeros(int $limite): void {
        if ($limite < 2 || $limite > 100) {
            throw new Exception("Limite deve estar entre 2 e 100!");
        }
        $this->limiteNumeros = $limite;
        echo "Limite de números ajustado para $limite.\n";
    }

    public function verHistorico(): void {
        if (empty($this->historico)) {
            echo "Nenhuma operação realizada ainda.\n";
            return;
        }
        echo "\n=== Histórico de Operações ===\n";
        foreach ($this->historico as $idx => $entry) {
            $numeros = implode(', ', $entry['numeros']);
            echo "[$idx] {$entry['operacao']} de [$numeros] = {$entry['resultado']} ({$entry['data']})\n";
        }
    }

    public function estatisticas(): void {
        if (empty($this->historico)) {
            echo "Nenhuma operação realizada para calcular estatísticas.\n";
            return;
        }
        $resultados = array_filter(array_column($this->historico, 'resultado'), 'is_numeric');
        if (empty($resultados)) {
            echo "Nenhum resultado numérico no histórico.\n";
            return;
        }
        $media = round(array_sum($resultados) / count($resultados), $this->precisaoDecimal);
        $maior = max($resultados);
        $menor = min($resultados);
        echo "\n=== Estatísticas ===\n";
        echo "Média dos resultados: $media\n";
        echo "Maior resultado: $maior\n";
        echo "Menor resultado: $menor\n";
    }

    public function exportarHistorico(): void {
        if (empty($this->historico)) {
            echo "Nenhum histórico para exportar.\n";
            return;
        }
        $filename = "historico_calculadora_" . date('Ymd_His') . ".txt";
        $content = "Histórico de Operações\n\n";
        foreach ($this->historico as $entry) {
            $numeros = implode(', ', $entry['numeros']);
            $content .= "{$entry['operacao']} de [$numeros] = {$entry['resultado']} ({$entry['data']})\n";
        }
        file_put_contents($filename, $content);
        echo "Histórico exportado para $filename.\n";
    }

    public function getLimiteNumeros(): int {
        return $this->limiteNumeros;
    }
}
 
function exibirMenu() {
    echo "\n=== Calculadora Avançada ===\n";
    echo "1. Somar\n";
    echo "2. Subtrair\n";
    echo "3. Multiplicar\n";
    echo "4. Dividir\n";
    echo "5. Ver Histórico\n";
    echo "6. Estatísticas\n";
    echo "7. Exportar Histórico\n";
    echo "8. Configurar Precisão Decimal\n";
    echo "9. Configurar Limite de Números\n";
    echo "10. Limpar Tela\n";
    echo "11. Sair\n";
    echo "Escolha uma opção (1-11): ";
    $opcao = trim(fgets(STDIN));
    return is_numeric($opcao) ? (int)$opcao : 0;
}
 
function capturarNumeros($limite) {
    while (true) {
        echo "Digite os números separados por espaço (máx. $limite, ex: 10 5 3): ";
        $entrada = trim(fgets(STDIN));
        if (empty($entrada)) {
            echo "Erro: Nenhuma entrada fornecida!\n";
            continue;
        }
        $numeros = explode(' ', $entrada);
        if (count($numeros) > $limite) {
            echo "Erro: Limite de $limite números excedido!\n";
            continue;
        }
        $numerosValidos = [];
        $valido = true;
        foreach ($numeros as $num) {
            $num = trim($num);
            if ($num === '' || !is_numeric($num)) {
                echo "Erro: '$num' não é um número válido!\n";
                $valido = false;
                break;
            }
            $valor = floatval($num);
            if ($valor > PHP_FLOAT_MAX || $valor < -PHP_FLOAT_MAX) {
                echo "Erro: '$valor' excede os limites numéricos!\n";
                $valido = false;
                break;
            }
            $numerosValidos[] = $valor;
        }
        if ($valido && count($numerosValidos) > 0) {
            return $numerosValidos;
        }
    }
}
 
function configurarValor($mensagem, $min, $max) {
    while (true) {
        echo "$mensagem ($min-$max): ";
        $valor = trim(fgets(STDIN));
        if (is_numeric($valor) && $valor >= $min && $valor <= $max) {
            return (int)$valor;
        }
        echo "Erro: Valor deve estar entre $min e $max!\n";
    }
}
 
$calc = new Calculadora();

while (true) {
    $opcao = exibirMenu();

    if ($opcao == 11) {
        echo "Saindo da calculadora. Até mais!\n";
        break;
    }

    switch ($opcao) {
        case 1:
        case 2:
        case 3:
        case 4:
            $numeros = capturarNumeros($calc->getLimiteNumeros());
            $operacao = match ($opcao) {
                1 => new Soma($numeros),
                2 => new Subtracao($numeros),
                3 => new Multiplicacao($numeros),
                4 => new Divisao($numeros),
            };
            $calc->executarOperacao($operacao);
            break;
        case 5:
            $calc->verHistorico();
            break;
        case 6:
            $calc->estatisticas();
            break;
        case 7:
            $calc->exportarHistorico();
            break;
        case 8:
            $precisao = configurarValor("Digite a precisão decimal", 0, 10);
            $calc->setPrecisaoDecimal($precisao);
            break;
        case 9:
            $limite = configurarValor("Digite o limite de números", 2, 100);
            $calc->setLimiteNumeros($limite);
            break;
        case 10:
            echo str_repeat("\n", 50);
            break;
        default:
            echo "Opção inválida! Escolha entre 1 e 11.\n";
    }
}

?>