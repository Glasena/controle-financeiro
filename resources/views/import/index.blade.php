<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Importação de CSV - Controle Financeiro</title>
</head>
<body>
    <h1>Importação de CSV</h1>
    <p>Escolha o tipo de importação:</p>
    <ul>
        <li><a href="{{ route('import.transactions.form') }}">Importar Transações Bancárias</a></li>
        <li><a href="{{ route('import.credit_card.form') }}">Importar Transações de Cartão de Crédito</a></li>
    </ul>
</body>
</html>
