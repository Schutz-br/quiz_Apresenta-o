
<?php
require_once __DIR__ . "/conexao.php";

try {
    $sql = "
        SELECT
            j.id,
            j.nick,

            COALESCE(SUM(
                CASE WHEN p.quiz = 'basico'
                THEN p.pontos ELSE 0 END
            ), 0) AS basico,

            COALESCE(SUM(
                CASE WHEN p.quiz = 'intermediario'
                THEN p.pontos ELSE 0 END
            ), 0) AS intermediario,

            COALESCE(SUM(
                CASE WHEN p.quiz = 'avancado'
                THEN p.pontos ELSE 0 END
            ), 0) AS avancado,

            COALESCE(SUM(p.pontos), 0) AS total

        FROM jogadores j

        LEFT JOIN pontuacoes p
            ON p.jogador_id = j.id

        GROUP BY j.id, j.nick

        ORDER BY total DESC, j.nick ASC
    ";

    $stmt = $pdo->query($sql);
    $jogadores = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Erro no ranking: " . $e->getMessage());
    $jogadores = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Ranking | Quiz</title>

    <meta http-equiv="refresh" content="10">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #101827;
            color: white;
            min-height: 100vh;
        }

        header {
            background: #182438;
            padding: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border-bottom: 1px solid #293950;
        }

        header h2 {
            color: #38bdf8;
        }

        header a {
            text-decoration: none;
            background: #2563eb;
            color: white;
            padding: 11px 20px;
            border-radius: 8px;
            font-weight: bold;
        }

        header a:hover {
            background: #1d4ed8;
        }

        .container {
            width: 92%;
            max-width: 1100px;
            margin: 45px auto;
        }

        .titulo {
            text-align: center;
            margin-bottom: 35px;
        }

        .titulo h1 {
            font-size: 36px;
            margin-bottom: 10px;
            color: #facc15;
        }

        .titulo p {
            color: #aab8ca;
        }

        .podio {
            display: flex;
            justify-content: center;
            align-items: end;
            gap: 15px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .podio-item {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 25px 15px;
            width: 190px;
            text-align: center;
        }

        .podio-item.primeiro {
            border: 2px solid #facc15;
            min-height: 190px;
        }

        .podio-item.segundo {
            border-color: #94a3b8;
        }

        .podio-item.terceiro {
            border-color: #c08457;
        }

        .medalha {
            font-size: 38px;
            margin-bottom: 12px;
        }

        .podio-item h3 {
            overflow-wrap: anywhere;
            margin-bottom: 10px;
        }

        .podio-item strong {
            color: #38bdf8;
            font-size: 22px;
        }

        .tabela-container {
            background: #1e293b;
            border-radius: 14px;
            padding: 20px;
            overflow-x: auto;
            border: 1px solid #334155;
        }

        .tabela-container h2 {
            margin-bottom: 20px;
            color: #38bdf8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th, td {
            padding: 16px 12px;
            text-align: center;
            border-bottom: 1px solid #334155;
        }

        th {
            background: #26364d;
            color: #38bdf8;
        }

        td.nick {
            text-align: left;
            font-weight: bold;
        }

        td.total {
            color: #facc15;
            font-weight: bold;
            font-size: 18px;
        }

        tr:hover {
            background: #26364d;
        }

        .vazio {
            text-align: center;
            padding: 35px;
            color: #aab8ca;
        }

        .atualizacao {
            text-align: center;
            color: #94a3b8;
            margin-top: 20px;
            font-size: 13px;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        @media (max-width: 600px) {
            .titulo h1 {
                font-size: 27px;
            }

            .podio-item {
                width: 100%;
            }

            header {
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>🏆 Quiz</h2>

    <a href="index.html">Voltar ao Quiz</a>
</header>

<main class="container">

    <section class="titulo">
        <h1>🏆 Ranking Geral</h1>
        <p>Confira os melhores jogadores e suas pontuações!</p>
    </section>

    <section class="podio">

        <?php
        $medalhas = [
            0 => ["🥇", "primeiro"],
            1 => ["🥈", "segundo"],
            2 => ["🥉", "terceiro"]
        ];

        foreach ($medalhas as $posicao => $medalha):
            if (!isset($jogadores[$posicao])) continue;

            $jogador = $jogadores[$posicao];
        ?>

            <div class="podio-item <?= $medalha[1] ?>">

                <div class="medalha">
                    <?= $medalha[0] ?>
                </div>

                <h3>
                    <?= htmlspecialchars(
                        $jogador["nick"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </h3>

                <strong>
                    <?= (int)$jogador["total"] ?> pontos
                </strong>

            </div>

        <?php endforeach; ?>

    </section>

    <section class="tabela-container">

        <h2>Classificação completa</h2>

        <?php if (count($jogadores) > 0): ?>

            <table>

                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Jogador</th>
                        <th>Básico</th>
                        <th>Intermediário</th>
                        <th>Avançado</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($jogadores as $i => $jogador): ?>

                        <tr>

                            <td>
                                <?= $i + 1 ?>º
                            </td>

                            <td class="nick">
                                <?= htmlspecialchars(
                                    $jogador["nick"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </td>

                            <td>
                                <?= (int)$jogador["basico"] ?>
                            </td>

                            <td>
                                <?= (int)$jogador["intermediario"] ?>
                            </td>

                            <td>
                                <?= (int)$jogador["avancado"] ?>
                            </td>

                            <td class="total">
                                <?= (int)$jogador["total"] ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="vazio">
                Ainda não existem jogadores no ranking.
                Seja o primeiro a participar!
            </div>

        <?php endif; ?>

    </section>

    <p class="atualizacao">
        O ranking é atualizado automaticamente a cada 10 segundos.
    </p>

</main>

<footer>
    Sistema de Quiz - Ranking de Jogadores
</footer>

</body>
</html>
