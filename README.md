# Locate database columns usage in a matter of seconds


---
## What it does?
Constella will automatically publish your DB columns as public PHP constants after every successful regular Laravel php artisan migrate execution.

## What this is about?
Wouldn't it be great for your developers, no matter if they are new to the project, or have been there for some time already,
to be able to find where a specific DB column is used on the codebase, super fast?

Think of it, that's extremelly powerful since almost everything is around data. 

That means:

- Faster bug fixes
- New developers can be more productive faster
- Faster / easier refactorings

And in general, more confidence on finding where things are being used in a large (or not) codebase.


### Example that describes the problem

Your project is a large SaaS application and your new developer needs to do some refactoring on a column named 'title' on a Project model.

Now the developer must find where this column is being used, probably exploring different files or globally searching on the IDE for things like:

```
project->title
```

```
where('title')
```

```
whereTitle
```

After finding some on different files, he/she just wishes that to be all. That simple task was almost already pain in the ass. The larger the codebase, the worse it gets.
The developer experience can get better IMO.

### A solution to the above problem

You have as a policy / coding standard on your codebase and you don't allow developers to use magic strings for DB columns. Developers should only use regular PHP constants.

The above developer now just searches for

```
ProjectColumn::TITLE
```

and immediately finds all the places where title columns is being used in a matter of seconds!

The trick here is to treat it as a strict code policy and see the long term benefits of this.

### Specifically

You **DON'T** using magic string for your columns, for example:

```
$project->title = 'something'
```

```
Project::query()->where('title', ...)
```

```
Task::query()->whereRelation('project', 'title', '=' ....)
```

You **FORCE** (on code reviews for example) the usage of consts:

```
$project->{ProjectColumn::TITLE} = 
```

```
Project::query()->where(ProjectColumn::TITLE', ...)
```


```
Task::query()->whereRelation('project', ProjectColumn::TITLE', '=' ....)
```

In the beginning that may be a bit weird to type, but you get used to it very fast and the long term benefits for the project really worth it IMO.

## Installation & usage

You can install the package via composer:

```bash
composer require glioympas/constella
```

You can publish the config file with:

```bash
php artisan vendor:publish --provider="Lioy\Constella\ConstellaServiceProvider" --tag="config"
````

Constella will automatically publish column classes on the same root folder where your models exist. 

This will happen after a successful regular migration using:

```
php artisan migrate (or migrate:rollback)
```

For first time usage, if you don't have an migration to execute, you can publish them using the following command:

```
php artisan constella:columns
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
