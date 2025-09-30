<?php

namespace Lioy\Constella\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Lioy\Constella\Actions\GetModelRelationsAction;
use Lioy\Constella\Actions\GetProjectModelsAction;

class GenerateModelRelations extends Command
{
    protected $signature = 'constella:relations';

    protected $description = 'Generates relation constants for application models';

    public function handle(
        GetProjectModelsAction $getProjectModelsAction,
        GetModelRelationsAction $getModelRelationsAction
    ): int
    {
        $projectModels = $getProjectModelsAction->execute();

        $projectModels->each(function(string $model) use ($getModelRelationsAction) {
            /** @var Model $model */
            $model = new $model;

            $relations = $getModelRelationsAction->execute($model);

            if(empty($relations)) {
                return;
            }

            $relationConstants = collect($relations)
                ->map(fn (string $relation) => 'public const '.strtoupper($this->camelCaseToSnakeCase($relation)).' = '."'{$relation}';"
                )
                ->toArray();

            $className = class_basename($model).config('constella.relation_class_suffix');

            $relationsFolder = config('constella.models_root_path')
                .'/'.config('constella.relation_classes_folder_name');

            if (! File::exists($relationsFolder)) {
                File::makeDirectory($relationsFolder);
            }



            File::put($relationsFolder."/$className.php", $this->generateFinalFileContents(
                className: $className,
                relationConstants: $relationConstants
            ));
        });

        return self::SUCCESS;
    }

    private function generateFinalFileContents(string $className, array $relationConstants): string
    {
        $template = $this->relationClassTemplate();

        return str_replace(
            search: [
                '{{className}}',
                '{{relationConstants}}',
            ],
            replace: [
                $className,
                implode('    '.PHP_EOL.'    ', $relationConstants),
            ],
            subject: $template
        );
    }

    private function camelCaseToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    private function relationClassTemplate(): string
    {
        return <<<EOT
        <?php

        namespace App\Models\Relations;

        class {{className}}
        {
            {{relationConstants}}
        }
        EOT;
    }
}
