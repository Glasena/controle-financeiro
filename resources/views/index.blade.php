<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Controle Financeiro</title>
</head>
<body>
    <h1>Controle Financeiro</h1>
    <p>Escolha uma opção:</p>
    <ul>
        <li>
            <a href="{{ route('transactions.index') }}">Transações Bancárias</a>
        </li>
        <li>
            <a href="{{ route('credit_card_transactions.index') }}">Transações de Cartão de Crédito</a>
        </li>
    </ul>
</body>
</html>
