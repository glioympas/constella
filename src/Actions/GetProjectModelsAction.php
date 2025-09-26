<?php

namespace Lioy\Constella\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class GetProjectModelsAction
{
    public function execute(): Collection
    {
        return collect(File::allFiles(config('constella.models_root_path')))
            ->map(function ($item) {
                $path = $item->getRelativePathName();

                $className = $this->extractNamespace($item).'\\'.basename($path);

                return str_replace('.php', '', $className);
            })
            ->filter(function ($class) {
                if (! class_exists($class)) {
                    return false;
                }

                $reflection = new ReflectionClass($class);

                if ($reflection->isAbstract()) {
                    return false;
                }

                return $reflection->isSubclassOf(Model::class);
            })
            ->values();
    }

    private function extractNamespace($file): ?string
    {
        $ns = null;
        $handle = fopen($file, 'r');

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }

        return $ns;
    }
}
