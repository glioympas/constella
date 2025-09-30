<?php

namespace Lioy\Constella\Tests\Unit\Actions;

use Lioy\Constella\Actions\GetModelRelationsAction;
use Lioy\Constella\Tests\Models\Task;
use Lioy\Constella\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class GetModelRelationsActionTest extends TestCase
{
    #[Test]
    public function it_returns_model_relations(): void
    {
        $task = new Task;

        $relations = app(GetModelRelationsAction::class)->execute($task);

        $this->assertEquals(['project', 'parentTask'], $relations);
    }
}
