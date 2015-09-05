<?php
namespace Triasrahman\JSONSeeder;

use DB;

trait JSONSeeder {

	protected function JSONSeed($table, $class, $relations = [])
    {
    	// $parent = class::parent;
    	DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::beginTransaction();

        try {

        	// Print the information banner
            $this->command->info("============================");
            $this->command->info("Seeding $table...");
            $this->command->info("============================");

            $this->deleteTable($table);

            // Check flush table on every relation
            foreach($relations as $name => $config) {

            	// if flush is true, delete flush the table
                if(isset($config['flush']) && $config['flush']) {

                	// flush the table from the table name config, or pluralize from the relation name
                	$this->deleteTable(isset($config['table']) ? $config['table'] : str_plural($name) );
                }
            }

            $collection = $this->getFromJSONFile($table);

            // Parse every object
            foreach ($collection as $object) {

                // Set the variables to empty
                $attributes = [];
                $syncs = [];
                $createMany = [];

                foreach($object as $key => $value) {

                    // Check if the value is not an array
                    if( ! is_array($value)) {

                        // Just parse the string
                        $attributes[$key] = $value;

                    }
                    // If not, check if it has a relation settings
                    elseif(isset($relations[$key])) {

                    	// Check if it has the 1st value (it means an array)
                        if(isset($value[0])) {

                            // So the relation is: "hasMany" / one to many
                            // $gets = [];

                            // Append to the create many array
                            $createMany[$key] = $value;

                        } else {

                        	// If not, the relation is: "belongsTo"

                        	// Insert the related row
                            $relationRow = $this->insertRelationRow($relations[$key]['class'], $key, $value);

                            // Update the relation row id
                            $attributes[$relations[$key]['local_key']] = $relationRow->id;
                        }
                    }
                }

                // Inserting to the model
                $stat = $class::create($attributes);
                $this->command->info("\nCreated:");
                $this->command->info($stat);

                // Check if must create many from the relations
                if(count($createMany)) {

                    foreach($createMany as $key => $rows) {

                        foreach ($rows as $row) {

                        	// Check if it's a many to many relation
                            if( isset($relations[$key]['many_to_many']) && $relations[$key]['many_to_many']) {

                            	// Insert the related row
                            	$relationRow = $this->insertRelationRow($relations[$key]['class'], $key, $row);

                            	// Append to the sync action
                                $syncs[$key][] = $relationRow->id;
                            }

                            else {

                            	// Add the foreign key
                                $row[$relations[$key]['foreign_key']] = $stat->id;

                                // Insert the related row
                                $relationRow = $this->insertRelationRow($relations[$key]['class'], $key, $row);
                            }
                        }
                    }
                }

                // If need sync action (for many to many)
                if(count($syncs)) {

                	$this->command->info("\nSynced:");

                	// Sync every related model
	                foreach($syncs as $key => $value) {
	                    $stat->$key()->sync($value);
	                    $this->command->info("- $key (" . count($value) .")");
	                }
	            }

                $this->command->comment("----------------------------");
            }

            DB::commit();

            $this->command->info("Seeding $table completed!\n");
        }
        catch (Exception $e) {
            $this->command->error("Error!");
            DB::rollback();
            $this->command->info("Rolling back...");
            $this->command->error($e);
            exit();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function deleteTable($table)
    {
    	try {
        	// Delete the table
            DB::table($table)->delete();
            $this->command->info("Table '$table' deleted");
        }
        catch(Exception $e) {
        	// Show error message
            $this->command->error("Table '$table' not found.");
            exit();
        }
    }

    private function insertRelationRow($class, $table, $value)
    {
    	foreach($value as $k => $a) {
	        $first[$k] = $a;
	        break;
	    }

    	// Check if relation row any
        $relationRow = $class::where(key($first), '=', $value[key($first)])->first();

        // If not found, insert the relation row
        if( ! $relationRow) {
            $relationRow = $class::create($value);
            $this->command->comment("\nCreated in '$table':");
            $this->command->comment($relationRow->toJson());
        }

        return $relationRow;
    }

    private function getFromJSONFile($table)
    {
    	// Open the file
        $file = \File::get(storage_path() . '/database/' . $table . '.json');

        if( ! $file) {
        	// If no file found, show the error
            $this->command->error("File $table.json not found.");
            exit();
        }

        // Decode the file to collection
        return json_decode($file, true);
    }
}