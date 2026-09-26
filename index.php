<?php
session_start();

require_once "conexao.php";

$pesquisa = "";

if (isset($_GET["pesquisa"])) {
    $pesquisa = trim($_GET["pesquisa"]);
}

if ($pesquisa != "") {

    $sql = "SELECT l.isbn, l.titulo, l.edição, l.custo, e.nome AS editora
            FROM livros l
            LEFT JOIN editora e ON e.publicacao = l.isbn
            WHERE l.titulo LIKE ?
            ORDER BY l.titulo";
    $stmt = $conexao->prepare($sql);
    $termo = "%" . $pesquisa . "%";
    $stmt->bind_param("s", $termo);
    $stmt->execute();

    $resultado = $stmt->get_result();
} else {
    $sql = "SELECT l.isbn, l.titulo, l.edição, l.custo, e.nome AS editora
            FROM livros l
            LEFT JOIN editora e ON e.publicacao = l.isbn
            ORDER BY l.titulo";
    $resultado = $conexao->query($sql);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dreams Books - Catálogo</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>Dreams Books</h1>
        <nav>
            <a href="index.php">Início</a>
            <?php if (isset($_SESSION["id"])): ?>
                <a href="conta.php">Minha conta</a>
                <?php if ($_SESSION["admin"] == 1): ?>
                    <a href="admin.php">Área admin</a>
                <?php endif; ?>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <h2>Pesquisar livros</h2>
        <form method="GET" class="pesquisa">
            <input
                type="text"
                name="pesquisa"
                placeholder="Digite o título do livro..."
                value="<?= htmlspecialchars($pesquisa) ?>"
            >
            <button type="submit">
                Pesquisar
            </button>
        </form>


        <?php if ($pesquisa != ""): ?>
            <h2>
                Resultados para:
                <strong><?= htmlspecialchars($pesquisa) ?></strong>
            </h2>
        <?php else: ?>
            <h2>Todos os livros</h2>
        <?php endif; ?>


        <?php if ($resultado->num_rows > 0): ?>
            <div class="livros">
                <?php while ($livro = $resultado->fetch_assoc()): ?>
                    <div class="livro">
                        <div class="livro-imagem">
                            📚
                        </div>

                        <h3>
                            <?= htmlspecialchars($livro["titulo"]) ?>
                        </h3>

                        <p class="detalhe">
                            Editora:
                            <?= $livro["editora"] != "" ? htmlspecialchars($livro["editora"]) : "Não informada" ?>
                        </p>

                        <p class="detalhe">
                            Edição: <?= htmlspecialchars($livro["edição"]) ?>
                        </p>

                        <p class="preco">
                            R$
                            <?= number_format($livro["custo"], 2, ",", ".") ?>
                        </p>

                        <form action="comprar.php" method="GET">
                            <input
                                type="hidden"
                                name="isbn"
                                value="<?= $livro["isbn"] ?>"
                            >
                            <button type="submit">
                                Comprar
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="nenhum">
                <p>
                    Nenhum livro encontrado.
                </p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>