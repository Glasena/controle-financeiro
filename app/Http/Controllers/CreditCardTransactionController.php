<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditCardTransaction;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;

class CreditCardTransactionController extends Controller
{
    // Exibe o formulário de importação de CSV ou PDF para transações de cartão
    public function importForm()
    {
        return view('import.credit_card');
    }

    // Processa o arquivo (CSV ou PDF) e insere os dados na tabela de transações de cartão
    public function import(Request $request)
    {
        $request->validate([
            'file'       => 'required|file|mimes:csv,txt,pdf',
            'bank'       => 'required|string|in:xp,bancodobrasil,nubank',
            'competence' => 'required|date_format:Y-m'
        ]);

        $bank       = $request->input('bank');
        $competence = $request->input('competence'); // formato "YYYY-MM"
        $overwrite  = $request->input('overwrite', false);
        $file       = $request->file('file');
        $dataToInsert = [];

        // Verifica se já existem registros para essa competência e banco (credit_card_name)
        $exists = CreditCardTransaction::where('competence', $competence)
                    ->where('credit_card_name', $bank)
                    ->exists();

        if ($exists && !$overwrite) {
            return redirect()->back()->withErrors([
                'overwrite' => 'Já existem registros para essa competência e banco. Confirme a sobrescrita.'
            ]);
        }

        if ($exists && $overwrite) {
            CreditCardTransaction::where('competence', $competence)
                ->where('credit_card_name', $bank)
                ->delete();
        }

        if ($bank === 'xp') {
            // Processamento para CSV da XP: delimitador ";" e colunas: Data, Descricao, Valor, Saldo
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

                try {
                    $date = Carbon::createFromFormat('d/m/Y', $record['Data']);
                } catch (\Exception $e) {
                    $date = now();
                }

                $amountStr = str_replace(['R$', ' '], '', $record['Valor']);
                $amountStr = str_replace('.', '', $amountStr);
                $amountStr = str_replace(',', '.', $amountStr);
                $amount = (float)$amountStr;

                $description = trim($record['Estabelecimento']);

                $dataToInsert[] = [
                    'credit_card_name'   => $bank,
                    'transaction_date'   => $date->toDateString(),
                    'description'        => $description,
                    'amount'             => $amount,
                    'installments_total' => null,
                    'installment_number' => null,
                    'competence'         => $competence,
                    'due_date'           => null,
                    'paid'               => false,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
            fclose($handle);
        }
        // Branch para PDF do Banco do Brasil (credit card transactions via PDF)
        elseif ($bank === 'bancodobrasil' && strtolower($file->getClientOriginalExtension()) === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();
            $lines = preg_split('/\r\n|\r|\n/', $text);

            foreach ($lines as $line) {
                $line = preg_replace('/\s+/', ' ', $line);
                // Regex: captura a data, descrição, opcionalmente parcela e valor.
                if (!preg_match('/^(\d{2}\/\d{2})\s+(.*?)(?:\s+Parcela\s+(\d+)\s*\/\s*(\d+))?\s+(-?R\$[\s-]*[\d\.,]+)/i', $line, $matches)) {
                    continue;
                }
                $datePart = $matches[1]; // ex: "31/12"
                $year = explode('-', $competence)[0]; // usa o ano da competência
                try {
                    $date = Carbon::createFromFormat('d/m/Y', $datePart . '/' . $year);
                } catch (\Exception $e) {
                    $date = now();
                }
                $description = mb_convert_encoding(trim($matches[2]), 'UTF-8', 'ISO-8859-1');
                $installment_number = isset($matches[3]) ? (int)$matches[3] : null;
                $installments_total = isset($matches[4]) ? (int)$matches[4] : null;
                $amountStr = str_replace(['R$', ' '], '', $matches[5]);
                $amountStr = str_replace('.', '', $amountStr);
                $amountStr = str_replace(',', '.', $amountStr);
                $amount = (float)$amountStr;

                $dataToInsert[] = [
                    'credit_card_name'   => $bank,
                    'transaction_date'   => $date->toDateString(),
                    'description'        => $description,
                    'amount'             => $amount,
                    'installments_total' => $installments_total,
                    'installment_number' => $installment_number,
                    'competence'         => $competence,
                    'due_date'           => null,
                    'paid'               => false,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
        }
        // Branch para PDF do Nubank
        elseif ($bank === 'nubank' && strtolower($file->getClientOriginalExtension()) === 'pdf') {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();
            $lines = preg_split('/\r\n|\r|\n/', $text);

            $monthMap = [
                'JAN' => '01',
                'FEV' => '02',
                'MAR' => '03',
                'ABR' => '04',
                'MAI' => '05',
                'JUN' => '06',
                'JUL' => '07',
                'AGO' => '08',
                'SET' => '09',
                'OUT' => '10',
                'NOV' => '11',
                'DEZ' => '12'
            ];

            foreach ($lines as $line) {
                $line = preg_replace('/\s+/', ' ', $line);
                if (!preg_match('/^\d{2}\s+[A-Z]{3}/', $line)) {
                    continue;
                }
                if (preg_match('/^(\d{2}\s+[A-Z]{3})\s+(.*?)(?:\s+Parcela\s+(\d+)\s*\/\s*(\d+))?\s+(-?R\$[\s-]*[\d\.,]+)\s*$/i', $line, $matches)) {
                    $datePart = $matches[1];
                    $parts = explode(' ', $datePart);
                    if (count($parts) < 2) continue;
                    $day = $parts[0];
                    $monthAbbr = strtoupper($parts[1]);
                    $month = isset($monthMap[$monthAbbr]) ? $monthMap[$monthAbbr] : '01';
                    $year = explode('-', $competence)[0];
                    $dateStr = $day . '/' . $month . '/' . $year;
                    try {
                        $date = Carbon::createFromFormat('d/m/Y', $dateStr);
                    } catch (\Exception $e) {
                        $date = now();
                    }
                    $description = mb_convert_encoding(trim($matches[2]), 'UTF-8', 'ISO-8859-1');
                    $installment_number = isset($matches[3]) ? (int)$matches[3] : null;
                    $installments_total = isset($matches[4]) ? (int)$matches[4] : null;
                    $amountStr = str_replace(['R$', ' '], '', $matches[5]);
                    $amountStr = str_replace('.', '', $amountStr);
                    $amountStr = str_replace(',', '.', $amountStr);
                    $amount = (float)$amountStr;

                    $dataToInsert[] = [
                        'credit_card_name'   => $bank,
                        'transaction_date'   => $date->toDateString(),
                        'description'        => $description,
                        'amount'             => $amount,
                        'installments_total' => $installments_total,
                        'installment_number' => $installment_number,
                        'competence'         => $competence,
                        'due_date'           => null,
                        'paid'               => false,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];
                }
            }
        }
        else {
            return back()->withErrors(['bank' => 'Formato de arquivo para este banco não implementado.']);
        }

        CreditCardTransaction::insert($dataToInsert);

        return redirect()->back()->with('success', 'Transações de cartão importadas com sucesso!');
    }

    public function checkExistence(Request $request)
    {
        $bank = $request->input('bank');
        $competence = $request->input('competence');
        $exists = \App\Models\CreditCardTransaction::where('competence', $competence)
                    ->where('credit_card_name', $bank)
                    ->exists();
        return response()->json(['exists' => $exists]);
    }
    

}
