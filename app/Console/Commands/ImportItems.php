<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\ItemType;
use Illuminate\Support\Facades\DB;

class ImportItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:items {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import items from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("❌ File not found: {$filePath}");
            $this->line("Please provide the full path to your CSV file.");
            $this->line("Example: php artisan import:items \"C:\\path\\to\\items.csv\"");
            return 1;
        }

        // Check file extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if ($extension === 'xlsx' || $extension === 'xls') {
            $this->error("❌ This command only works with CSV files.");
            $this->newLine();
            $this->line("To convert your Excel file to CSV:");
            $this->line("1. Open your Excel file");
            $this->line("2. Go to File → Save As");
            $this->line("3. Choose 'CSV (Comma delimited) (*.csv)'");
            $this->line("4. Save it and run this command again with the .csv file");
            return 1;
        }

        $this->info("📁 Starting import from: {$filePath}");
        $this->newLine();

        DB::beginTransaction();

        try {
            // Open the CSV file
            $handle = fopen($filePath, 'r');
            
            if ($handle === false) {
                throw new \Exception("Could not open file: {$filePath}");
            }

            $categoryCache = [];
            $rowCount = 0;
            $skippedRows = 0;
            $lineNumber = 0;

            while (($data = fgetcsv($handle)) !== false) {
                $lineNumber++;
                
                // Skip empty rows
                if (empty($data) || (count($data) === 1 && empty(trim($data[0])))) {
                    $skippedRows++;
                    continue;
                }

                // Skip header row (first line)
                if ($lineNumber === 1) {
                    $firstCell = strtolower(trim($data[0] ?? ''));
                    if ($firstCell === 'no' || $firstCell === 'item' || $firstCell === 'category') {
                        $this->line("📋 Detected header row, skipping...");
                        continue;
                    }
                }

                // Check if we have enough columns
                if (count($data) < 3) {
                    $this->warn("⚠️  Line {$lineNumber}: Skipping - not enough columns");
                    $skippedRows++;
                    continue;
                }

                // Extract data from columns
                $itemNumber = trim($data[0]);  // Column 1: No
                $itemName = trim($data[1]);    // Column 2: item
                $categoryName = trim($data[2]); // Column 3: category

                // Validate data
                if (empty($itemName)) {
                    $this->warn("⚠️  Line {$lineNumber}: Skipping - empty item name");
                    $skippedRows++;
                    continue;
                }

                if (empty($categoryName)) {
                    $this->warn("⚠️  Line {$lineNumber}: Skipping - empty category");
                    $skippedRows++;
                    continue;
                }

                // Normalize category name (lowercase, trim)
                $categoryName = strtolower(trim($categoryName));

                // Get or create item type
                if (!isset($categoryCache[$categoryName])) {
                    $itemType = ItemType::firstOrCreate(
                        ['name' => $categoryName],
                        ['name' => $categoryName]
                    );
                    $categoryCache[$categoryName] = $itemType->id;
                    $this->line("✅ Created category: {$categoryName}");
                }

                // Create or update item
                $item = Item::firstOrCreate(
                    ['name' => $itemName],
                    [
                        'item_type_id' => $categoryCache[$categoryName],
                        'name' => $itemName
                    ]
                );

                if ($item->wasRecentlyCreated) {
                    $this->line("📦 Imported: {$itemName}");
                    $rowCount++;
                } else {
                    $this->line("↻ Already exists: {$itemName}");
                }
            }

            fclose($handle);
            DB::commit();

            // Display summary
            $this->newLine();
            $this->info("🎉 IMPORT COMPLETED SUCCESSFULLY!");
            $this->info("====================================");
            $this->info("Total items processed: {$rowCount}");
            $this->info("Categories created: " . count($categoryCache));
            
            if ($skippedRows > 0) {
                $this->warn("Rows skipped: {$skippedRows}");
            }

            // Show categories summary
            $this->newLine();
            $this->info("📋 CATEGORIES IMPORTED:");
            foreach ($categoryCache as $category => $id) {
                $this->line("   • {$category} (ID: {$id})");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->newLine();
            $this->error('❌ IMPORT FAILED!');
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}