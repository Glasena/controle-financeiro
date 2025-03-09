<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Exibe o formulário de importação de CSV para transações.
     */
    public function importForm()
    {
        return view('import.transactions');
    }

    /**
     * Processa o arquivo CSV e insere as transações no banco de dados.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file'       => 'required|file|mimes:csv,txt',
            'bank'       => 'required|string|in:bancodobrasil,xp,nubank',
            'competence' => 'required|date_format:Y-m'
        ]);

        $bank       = $request->input('bank');
        $competence = $request->input('competence'); // ex: "2025-02"
        $file       = $request->file('file');
        $dataToInsert = [];

        if ($bank === 'xp') {
            // CSV da XP: delimitador ";" e colunas: Data, Descricao, Valor, Saldo
            $delimiter = ';';
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                return back()->withErrors(['file' => 'Erro ao abrir o arquivo.']);
            }
            $header = fgetcsv($handle, 1000, $delimiter);
            if (!$header) {
                return back()->withErrors(['file' => 'Arquivo CSV inválido.']);
            }
            $header = array_map('trim', $header);

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $record = array_combine($header, $row);

                // Data no formato "dd/mm/YYYY"
                try {
                    $date = Carbon::createFromFormat('d/m/Y', $record['Data']);
                } catch (\Exception $e) {
                    $date = now();
                }

                // Processa o valor: remove "R$", espaços, separadores e troca vírgula por ponto
                $amountStr = str_replace(['R$', ' '], '', $record['Valor']);
                $amountStr = str_replace('.', '', $amountStr);
                $amountStr = str_replace(',', '.', $amountStr);
                $amount = (float)$amountStr;

                $description = trim($record['Descricao']);

                $dataToInsert[] = [
                    'transaction_date' => $date->toDateString(),
                    'description'      => $description,
                    'amount'           => $amount,
                    'bank'             => $bank,
                    'type'             => 'normal',
                    'competence'       => $competence,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
            fclose($handle);
        }
        elseif ($bank === 'bancodobrasil') {
            // CSV do Banco do Brasil: delimitador "," e colunas: Data, Lançamento, Detalhes, N° documento, Valor, Tipo Lançamento
            $delimiter = ',';
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                return back()->withErrors(['file' => 'Erro ao abrir o arquivo.']);
            }
            $header = fgetcsv($handle, 1000, $delimiter);
            if (!$header) {
                return back()->withErrors(['file' => 'Arquivo CSV inválido.']);
            }
            $header = array_map(function ($item) {
                $item = mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1');
                return trim($item, "\" \t\n\r\0\x0B");
            }, $header);

            if (!in_array('Lançamento', $header)) {
                return back()->withErrors(['file' => 'O CSV não possui a coluna "Lançamento".']);
            }

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $record = array_combine($header, $row);

                // Pula linhas cujo Lançamento seja "Saldo Anterior", "Saldo do dia" ou "Saldo"
                $lancamento = strtolower(trim($record['Lançamento']));
                if (in_array($lancamento, ['saldo anterior', 'saldo do dia', 'saldo'])) {
                    continue;
                }

                $dateString = trim($record['Data']);
                if (empty($dateString)) {
                    continue;
                }
                try {
                    $date = Carbon::createFromFormat('d/m/Y', $dateString);
                } catch (\Exception $e) {
                    continue;
                }
                if ($date->year < 1900) {
                    continue;
                }

                $lancamentoUTF8 = mb_convert_encoding(trim($record['Lançamento']), 'UTF-8', 'ISO-8859-1');
                $detalhesUTF8   = mb_convert_encoding(trim($record['Detalhes']), 'UTF-8', 'ISO-8859-1');

                $description = $lancamentoUTF8;
                if (!empty($detalhesUTF8)) {
                    $description .= ' - ' . $detalhesUTF8;
                }

                $amountStr = trim($record['Valor']);
                $amountStr = str_replace(['R$', ' '], '', $amountStr);
                $amountStr = str_replace('.', '', $amountStr);
                $amountStr = str_replace(',', '.', $amountStr);
                $amount = (float)$amountStr;

                $dataToInsert[] = [
                    'transaction_date' => $date->toDateString(),
                    'description'      => $description,
                    'amount'           => $amount,
                    'bank'             => $bank,
                    'type'             => 'normal',
                    'competence'       => $competence,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
            fclose($handle);
        }
        elseif ($bank === 'nubank') {
            // CSV do Nubank: delimitador ",", colunas: Data, Valor, Identificador, Descrição
            $delimiter = ',';
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                return back()->withErrors(['file' => 'Erro ao abrir o arquivo.']);
            }
            $header = fgetcsv($handle, 1000, $delimiter);
            if (!$header) {
                return back()->withErrors(['file' => 'Arquivo CSV inválido.']);
            }
            $header = array_map('trim', $header);
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                $record = array_combine($header, $row);

                try {
                    $date = Carbon::createFromFormat('d/m/Y', $record['Data']);
                } catch (\Exception $e) {
                    $date = now();
                }
                $amount = (float) $record['Valor'];

                $description = trim($record['Descrição']);

                $dataToInsert[] = [
                    'transaction_date' => $date->toDateString(),
                    'description'      => $description,
                    'amount'           => $amount,
                    'bank'             => $bank,
                    'type'             => 'normal',
                    'competence'       => $competence,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
            fclose($handle);
        }
        else {
            return back()->withErrors(['bank' => 'Formato de arquivo para este banco não implementado.']);
        }

        Transaction::insert($dataToInsert);

        return redirect()->back()->with('success', 'Transações importadas com sucesso!');
    }
}
