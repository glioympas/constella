<?php

namespace Lioy\Constella\Tests\Unit\Console;

use Illuminate\Support\Facades\Config;
use Lioy\Constella\Actions\GetProjectModelsAction;
use Lioy\Constella\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class GenerateModelRelationsTest extends TestCase
{
    #[Test]
    public function it_generates_model_relation_constants(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../Models/Migrations/');
        $this->artisan('migrate');

        Config::set('constella.models_root_path', __DIR__.'/../../Models');

        $relationsFolderPath = config('constella.models_root_path').'/Relations';

        $projectRelationFile = $relationsFolderPath.'/ProjectRelation.php';
        $taskRelationFile = $relationsFolderPath.'/TaskRelation.php';

        @unlink($taskRelationFile);

        $this->artisan('constella:relations');

        $this->assertFileDoesNotExist($projectRelationFile); // No relations on Project model
        $this->assertFileExists($taskRelationFile);

        $this->assertSame($this->taskRelationContents(), file_get_contents($taskRelationFile));

        @unlink($taskRelationFile);
        rmdir($relationsFolderPath);
    }

    private function taskRelationContents(): string
    {
        $space = '    ';

        return <<<EOT
        <?php

        namespace App\Models\Relations;

        class TaskRelation
        {
            public const PROJECT = 'project';$space
            public const PARENT_TASK = 'parentTask';
        }
        EOT;
    }
}
