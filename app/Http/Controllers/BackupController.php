<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * បង្ហាញបញ្ជីឯកសារ Backup ទាំងអស់
     */
    public function index()
    {
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $files = Storage::files('backups');
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => round(Storage::size($file) / 1024, 2) . ' KB',
                'date' => date('Y-m-d H:i:s', Storage::lastModified($file)),
            ];
        }

        // តម្រៀបឯកសារពីថ្មីទៅចាស់
        usort($backups, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        return view('backups.index', compact('backups'));
    }

    /**
     * បង្កើតឯកសារ Backup (.sql) ថ្មី
     */
    public function create()
    {
        try {
            if (!Storage::exists('backups')) {
                Storage::makeDirectory('backups');
            }

            // ១. ទាញយកឈ្មោះតារាង (Tables) ទាំងអស់ក្នុង Database
            $tables = array_map('current', DB::select('SHOW TABLES'));

            $sql = "-- Laravel POS Database Backup\n";
            $sql .= "-- Generated at: " . now()->toDateTimeString() . "\n\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // ២. ទាញយកកូដរចនាសម្ព័ន្ធតារាង (Table Structure)
                $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
                $createTableSql = $createTable[0]->{'Create Table'} ?? $createTable[0]->{'Create View'} ?? '';

                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createTableSql . ";\n\n";

                // ៣. ទាញយកទិន្នន័យក្នុងតារាង (Table Data)
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowArray = (array)$row;
                    $columns = array_keys($rowArray);

                    // កែសម្រួលតម្លៃពិសេស (Special characters / Null values)
                    $values = array_map(function($value) {
                        if (is_null($value)) return 'NULL';
                        return "'" . addslashes($value) . "'";
                    }, array_values($rowArray));

                    $sql .= "INSERT INTO `{$table}` (`" . implode("`, `", $columns) . "`) VALUES (" . implode(", ", $values) . ");\n";
                }
                $sql .= "\n\n";
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

            // ៤. រក្សាទុកឯកសារទៅក្នុង storage/app/backups/
            $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
            Storage::put('backups/' . $filename, $sql);

            return redirect()->route('backups.index')->with('success', 'រក្សាទុកទិន្នន័យ (Backup) បានជោគជ័យ។');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')->with('error', 'មានបញ្ហាក្នុងការរក្សាទុកទិន្នន័យ៖ ' . $e->getMessage());
        }
    }

    /**
     * ទាញយកឯកសារ Backup
     */
    public function download($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }
        return redirect()->route('backups.index')->with('error', 'រកមិនឃើញឯកសារទិន្នន័យឡើយ។');
    }

    /**
     * ស្ដារទិន្នន័យឡើងវិញ (Restore Database)
     */
    public function restore($filename)
    {
        try {
            $path = 'backups/' . $filename;
            if (!Storage::exists($path)) {
                return redirect()->route('backups.index')->with('error', 'រកមិនឃើញឯកសារទិន្នន័យឡើយ។');
            }

            $sql = Storage::get($path);

            // រត់កូដ SQL ទាំងអស់ដើម្បីដំឡើង Database ឡើងវិញ
            DB::unprepared($sql);

            return redirect()->route('backups.index')->with('success', 'ស្ដារទិន្នន័យ (Restore) ត្រឡប់មកវិញបានជោគជ័យ។');
        } catch (\Exception $e) {
            return redirect()->route('backups.index')->with('error', 'មានបញ្ហាក្នុងការស្ដារទិន្នន័យ៖ ' . $e->getMessage());
        }
    }

    /**
     * លុបឯកសារ Backup
     */
    public function destroy($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::exists($path)) {
            Storage::delete($path);
            return redirect()->route('backups.index')->with('success', 'លុបឯកសារទិន្នន័យបានជោគជ័យ។');
        }
        return redirect()->route('backups.index')->with('error', 'រកមិនឃើញឯកសារទិន្នន័យដើម្បីលុបឡើយ។');
    }
}
