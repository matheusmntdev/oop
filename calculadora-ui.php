<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Calculadora Avançada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .section {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            margin-right: 10px;
        }
        input, select, button {
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .resultado {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Calculadora Avançada</h1>

        <!-- Seção de Operações -->
        <div class="section">
            <h2>Realizar Operação</h2>
            <form method="post" action="">
                <label>Números (separados por espaço):</label><br>
                <input type="text" name="numeros" placeholder="ex: 10 5 3" required style="width: 300px;"><br>
                <label>Operação:</label>
                <select name="operacao">
                    <option value="somar">Somar</option>
                    <option value="subtrair">Subtrair</option>
                    <option value="multiplicar">Multiplicar</option>
                    <option value="dividir">Dividir</option>
                </select><br>
                <button type="submit" name="calcular">Calcular</button>
            </form>
        </div>

        <!-- Configurações -->
        <div class="section">
            <h2>Configurações</h2>
            <form method="post" action="">
                <label>Precisão Decimal (0-10):</label>
                <input type="number" name="precisao" min="0" max="10" value="<?php echo isset($_SESSION['precisao']) ? $_SESSION['precisao'] : 2; ?>">
                <button type="submit" name="set_precisao">Aplicar</button><br>
                <label>Limite de Números (2-100):</label>
                <input type="number" name="limite" min="2" max="100" value="<?php echo isset($_SESSION['limite']) ? $_SESSION['limite'] : 10; ?>">
                <button type="submit" name="set_limite">Aplicar</button>
            </form>
        </div>

        <!-- Resultado e Histórico -->
        <?php
        session_start();

        // Inicializa sessão se necessário
        if (!isset($_SESSION['historico'])) {
            $_SESSION['historico'] = [];
            $_SESSION['precisao'] = 2;
            $_SESSION['limite'] = 10;
        }

        abstract class Operacao {
            protected $nome;
            protected $numeros;

            public function __construct(array $numeros) {
                $this->numeros = $numeros;
            }
            abstract public function calcular(): float|string;
            public function getNome(): string { return $this->nome; }
            public function getNumeros(): array { return $this->numeros; }
        }

        class Soma extends Operacao {
            protected $nome = "Soma";
            public function calcular(): float|string {
                return empty($this->numeros) ? "Erro: Nenhum número!" : array_sum($this->numeros);
            }
        }

        class Subtracao extends Operacao {
            protected $nome = "Subtração";
            public function calcular(): float|string {
                if (empty($this->numeros)) return "Erro: Nenhum número!";
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
                if (empty($this->numeros)) return "Erro: Nenhum número!";
                $resultado = 1;
                foreach ($this->numeros as $num) $resultado *= $num;
                return $resultado;
            }
        }

        class Divisao extends Operacao {
            protected $nome = "Divisão";
            public function calcular(): float|string {
                if (empty($this->numeros)) return "Erro: Nenhum número!";
                $resultado = $this->numeros[0];
                for ($i = 1; $i < count($this->numeros); $i++) {
                    if ($this->numeros[$i] == 0) return "Erro: Divisão por zero!";
                    $resultado /= $this->numeros[$i];
                }
                return $resultado;
            }
        }

        class Calculadora {
            private $historico;
            private $precisao;
            private $limite;

            public function __construct() {
                $this->historico = &$_SESSION['historico'];
                $this->precisao = &$_SESSION['precisao'];
                $this->limite = &$_SESSION['limite'];
            }

            public function executarOperacao(Operacao $operacao): void {
                $resultado = $operacao->calcular();
                if (is_numeric($resultado)) {
                    $resultado = round($resultado, $this->precisao);
                }
                $this->historico[] = [
                    'operacao' => $operacao->getNome(),
                    'numeros' => $operacao->getNumeros(),
                    'resultado' => $resultado,
                    'data' => date('Y-m-d H:i:s')
                ];
                echo "<div class='resultado'>Resultado da {$operacao->getNome()}: $resultado</div>";
            }

            public function validarNumeros($input): array {
                $numeros = array_map('trim', explode(' ', $input));
                if (count($numeros) > $this->limite) {
                    throw new Exception("Erro: Limite de {$this->limite} números excedido!");
                }
                $validos = [];
                foreach ($numeros as $num) {
                    if (!is_numeric($num)) throw new Exception("Erro: '$num' não é um número válido!");
                    $validos[] = floatval($num);
                }
                return $validos;
            }

            public function mostrarHistorico(): void {
                if (empty($this->historico)) {
                    echo "<div class='resultado'>Nenhum histórico disponível.</div>";
                    return;
                }
                echo "<h2>Histórico</h2><table><tr><th>Operação</th><th>Números</th><th>Resultado</th><th>Data</th></tr>";
                foreach ($this->historico as $entry) {
                    $numeros = implode(', ', $entry['numeros']);
                    echo "<tr><td>{$entry['operacao']}</td><td>$numeros</td><td>{$entry['resultado']}</td><td>{$entry['data']}</td></tr>";
                }
                echo "</table>";
            }
        }

        $calc = new Calculadora();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (isset($_POST['calcular'])) {
                    $numeros = $calc->validarNumeros($_POST['numeros']);
                    $operacao = match ($_POST['operacao']) {
                        'somar' => new Soma($numeros),
                        'subtrair' => new Subtracao($numeros),
                        'multiplicar' => new Multiplicacao($numeros),
                        'dividir' => new Divisao($numeros),
                    };
                    $calc->executarOperacao($operacao);
                }
                if (isset($_POST['set_precisao'])) {
                    $precisao = (int)$_POST['precisao'];
                    if ($precisao < 0 || $precisao > 10) throw new Exception("Precisão deve estar entre 0 e 10!");
                    $_SESSION['precisao'] = $precisao;
                    echo "<div class='resultado'>Precisão ajustada para $precisao casas.</div>";
                }
                if (isset($_POST['set_limite'])) {
                    $limite = (int)$_POST['limite'];
                    if ($limite < 2 || $limite > 100) throw new Exception("Limite deve estar entre 2 e 100!");
                    $_SESSION['limite'] = $limite;
                    echo "<div class='resultado'>Limite ajustado para $limite números.</div>";
                }
            } catch (Exception $e) {
                echo "<div class='resultado' style='color: red;'>{$e->getMessage()}</div>";
            }
        }

        $calc->mostrarHistorico();
        ?>
    </div>
</body>
</html>