<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Items;
use App\Models\ItemType;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportItemsFromExcel extends Command
{
    protected $signature = 'import:excel {file}';
    protected $description = 'Import items from Excel file';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("📁 Importing from: {$filePath}");
        $this->newLine();

        DB::beginTransaction();

        try {
            $data = Excel::toArray([], $filePath);
            $rows = $data[0];
            
            $categoryCache = [];
            $itemsImported = 0;

            $this->info("Starting import...");
            $this->newLine();

            foreach ($rows as $index => $row) {
                // Skip header
                if ($index === 0) {
                    $this->line("Skipping header row");
                    continue;
                }

                $itemName = trim($row[1] ?? '');
                $categoryName = trim($row[2] ?? '');

                if (empty($itemName) || empty($categoryName)) {
                    $this->warn("Skipping row {$index}: missing data");
                    continue;
                }

                $categoryName = strtolower($categoryName);
                $this->line("Processing: '{$itemName}' → '{$categoryName}'");

                // CREATE OR GET ITEM TYPE
                if (!isset($categoryCache[$categoryName])) {
                    $itemType = ItemType::firstOrCreate(
                        ['name' => $categoryName],
                        ['name' => $categoryName]
                    );
                    $categoryCache[$categoryName] = $itemType->id;
                    $this->info("✓ Category '{$categoryName}' = ID {$itemType->id}");
                }

                $itemTypeId = $categoryCache[$categoryName];

                // CREATE ITEM - Use the correct relationship name
                $item = Items::firstOrCreate(
                    ['name' => $itemName],
                    [
                        'item_type_id' => $itemTypeId,
                        'name' => $itemName
                    ]
                );

                $itemsImported++;
                $this->line("✓ Item '{$itemName}' (item_type_id: {$itemTypeId})");
            }

            DB::commit();

            // SHOW RESULTS - FIXED: Use 'item_type' not 'itemType'
            $this->newLine();
            $this->info("✅ IMPORT COMPLETE!");
            $this->info("==================");
            
            $this->info("Item Types created:");
            $types = ItemType::all();
            foreach ($types as $type) {
                $count = Items::where('item_type_id', $type->id)->count();
                $this->line("  ID {$type->id}: {$type->name} ({$count} items)");
            }

            $this->newLine();
            $this->info("Sample items from database:");
            
            // FIX: Use 'item_type' relationship (snake_case)
            $sampleItems = Items::with('item_type')->limit(5)->get();
            
            foreach ($sampleItems as $item) {
                // Check if relationship is loaded
                if ($item->item_type) {
                    $categoryName = $item->item_type->name;
                } else {
                    $categoryName = 'NO CATEGORY (check relationship)';
                }
                
                $this->line("  '{$item->name}'");
                $this->line("    → item_type_id: {$item->item_type_id}");
                $this->line("    → Category: {$categoryName}");
                $this->line("  ---");
            }

            $this->newLine();
            $this->info("📊 Summary:");
            $this->line("Total items imported: {$itemsImported}");
            $this->line("Total categories: " . count($categoryCache));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error: " . $e->getMessage());
            
            if (strpos($e->getMessage(), 'itemType') !== false) {
                $this->newLine();
                $this->warn("Relationship error! Your Items model has 'item_type()' not 'itemType()'");
                $this->line("Check your Items model - the method should be:");
                $this->line("public function item_type() { // snake_case");
                $this->line("    return \$this->belongsTo(ItemType::class);");
                $this->line("}");
            }
            
            return 1;
        }

        return 0;
    }
}