<?php

namespace Lioy\Constella\Console;

use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Info;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Lioy\Constella\Actions\GetProjectModelsAction;

class GenerateModelColumns extends Command
{
    protected $signature = 'constella:columns';

    protected $description = 'Generates column constants for application models';

    public function handle(GetProjectModelsAction $getProjectModelsAction): int
    {
        $projectModels = $getProjectModelsAction->execute();

        $projectModels->each(function (string $model) {
            /** @var Model $model */
            $model = new $model;

            $columnConstants = collect(Schema::getColumns($model->getTable()))
                ->map(fn (array $column) => 'public const '.strtoupper($this->camelCaseToSnakeCase($column['name'])).' = '."'{$column['name']}';"
                )
                ->toArray();

            $className = class_basename($model).config('constella.column_class_suffix');

            $columnsFolder = config('constella.models_root_path')
                .'/'.config('constella.column_classes_folder_name');

            if (! File::exists($columnsFolder)) {
                File::makeDirectory($columnsFolder);
            }

            File::put($columnsFolder."/$className.php", $this->generateFinalFileContents(
                className: $className,
                columnConstants: $columnConstants
            ));
        });

        (new Info($this->output))->render('[Constella]: Model column constants files generated successfully.');

        return self::SUCCESS;
    }

    private function generateFinalFileContents(string $className, $columnConstants): string
    {
        $template = $this->columnClassTemplate();

        return str_replace(
            search: [
                '{{className}}',
                '{{columnConstants}}',
            ],
            replace: [
                $className,
                implode('    '.PHP_EOL.'    ', $columnConstants),
            ],
            subject: $template
        );
    }

    private function camelCaseToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    private function columnClassTemplate(): string
    {
        return <<<EOT
        <?php

        namespace App\Models\Columns;

        class {{className}}
        {
            {{columnConstants}}
        }
        EOT;
    }
}
