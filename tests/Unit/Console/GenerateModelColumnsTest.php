<?php

namespace Lioy\Constella\Tests\Unit\Console;

use Illuminate\Support\Facades\Config;
use Lioy\Constella\Actions\GetProjectModelsAction;
use Lioy\Constella\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class GenerateModelColumnsTest extends TestCase
{
    #[Test]
    public function it_generates_model_constants(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../Models/Migrations/');
        $this->artisan('migrate');

        Config::set('constella.models_root_path', __DIR__.'/../../Models');

        $columnsFolderPath = config('constella.models_root_path').'/Columns';

        $projectColumnFile = $columnsFolderPath.'/ProjectColumn.php';
        $taskColumnFile = $columnsFolderPath.'/TaskColumn.php';

        @unlink($projectColumnFile);
        @unlink($taskColumnFile);

        $this->artisan('constella:columns');

        $this->assertFileExists($projectColumnFile);
        $this->assertFileExists($taskColumnFile);

        $this->assertSame($this->projectColumnContents(), file_get_contents($projectColumnFile));
        $this->assertSame($this->taskColumnContents(), file_get_contents($taskColumnFile));

        @unlink($projectColumnFile);
        @unlink($taskColumnFile);
        rmdir($columnsFolderPath);
    }

    #[Test]
    public function it_is_being_executed_after_migrations(): void
    {
        $this->mock(GetProjectModelsAction::class, function (MockInterface $mock) {
            $mock->expects('execute')
                ->andReturn(collect());
        });

        Config::set('app.env', 'local');

        $this->loadMigrationsFrom(__DIR__.'/../../Models/Migrations/');

        $this->artisan('migrate:fresh');
    }

    private function projectColumnContents(): string
    {
        return <<<EOT
        <?php

        namespace App\Models\Columns;

        class ProjectColumn
        {
            public const ID = 'id';    
            public const PROJECT_NAME = 'projectName';    
            public const CREATED_AT = 'created_at';    
            public const UPDATED_AT = 'updated_at';
        }
        EOT;
    }

    private function taskColumnContents(): string
    {
        return <<<EOT
        <?php

        namespace App\Models\Columns;

        class TaskColumn
        {
            public const ID = 'id';    
            public const NAME = 'name';    
            public const PROJECT_ID = 'project_id';    
            public const CREATED_AT = 'created_at';    
            public const UPDATED_AT = 'updated_at';
        }
        EOT;
    }
}
