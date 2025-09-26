<?php

namespace Lioy\Constella\Tests\Unit\Actions;

use Illuminate\Support\Facades\Config;
use Lioy\Constella\Actions\GetProjectModelsAction;
use Lioy\Constella\Tests\Models\Project;
use Lioy\Constella\Tests\Models\Task;
use Lioy\Constella\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GetProjectModelsActionTest extends TestCase
{
    #[Test]
    public function it_returns_non_abstract_models(): void
    {
        Config::set('constella.models_root_path', __DIR__.'/../../Models');

        $modelsFound = app(GetProjectModelsAction::class)
            ->execute()
            ->toArray();

        $this->assertCount(2, $modelsFound);

        $modelsNeeded = [
            Project::class,
            Task::class,
        ];

        $this->assertEquals([], array_diff($modelsFound, $modelsNeeded));
    }
}
