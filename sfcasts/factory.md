# Factory Pattern

* work on the definition
* Anatomy of the pattern?
* different variants of the pattern
  * factory with multiple make methods
  * factory with argument constructor
  * abstract factory
* Explain the coding example
* Say something like we'll implement the simplest factory possible and then we'll
upgrade it to an Abstract factory
* Explain abstract factories: they create families of objects
* Using facotries with Symfony
* Factories in the real world
* Conclusion
  * this pattern protects your code from changing if you modify how your objects
  are instantiated
  * but if you add new object types, you need to change the factory and the code that uses it

Definition: is a creational design pattern that lets you produce families of related
objects without specifying their concrete classes.

Another definition with help of ChatGPT: The Abstract Factory Pattern is a creational design pattern
that provides an interface for creating families of related or dependent objects without
specifying their concrete classes. This pattern is particularly useful when a system
needs to be independent of how its objects are created, composed, or represented.

You use a factory when you need to instantiate different implementations of the same interface
but you don't want the callers to know how they are constructed.

- Leverages DIP because it relies on abstractions/interfaces instead of concrete classes
- Leverages SRP because it separates the code that creates objects from the code that uses them
- Leverages OCP because it allows you to introduce new types of builders without modifying the code that uses them
  it allows you to close the code that interacts with the objects build by the factories

However, this pattern does not protect you from changing more than one place when you add new object types
because you'd need to change or add another make method to the factory, and you'd also to update
the caller's code to use it.

There are a few variants of this pattern:

## Factory with multiple make methods

```php
class WeaponFactory
{
    public function makeSword(): WeaponInterface
    {
        return new Sword(Dice::rollRange(4, 8), 12);
    }

    public function makeAxe(int $bonusDamage = 0): WeaponInterface
    {
        return new Axe($bonusDamage + Dice::rollRange(6, 12), 8);
    }
}
```

In this approach we create a "make" method per each derivate of the interface. This is useful when
the caller already knows what object it needs. Also, the make methods can have different arguments

## Factory with argument constructor

```php
class WeaponFactory
{
    public function make(string $type): WeaponInterface
    {
        return match ($type) {
            'bow' => new Bow(Dice::rollRange(3, 6), 20),
            'fire_bolt' => new Sword(Dice::rollRange(4, 8), 12),
            'sword' => new Axe(Dice::rollRange(6, 12), 8),
            default => throw new \RuntimeException('Invalid weapon type given')
        };
    }
}
```

In this approach we have a single method that receives an argument that determines what object to create.
This is useful when the application is more dynamic, the `$type` value can come from the user's input, or
from the request, or something else.

Con: you lose type safety because you can send any string as the type, but that can be solved
with a good test suite, or using enums. Also, it is harder to have different constructor arguments per type

## Factory Method

```php
interface WeaponFactoryInterface
{
    protected function makeSword(): WeaponInterface;

    protected function makeAxe(): WeaponInterface;
}

class SteelWeaponFactory implements WeaponFactoryInterface
{
    protected function makeSword(): WeaponInterface
    {
        return new SteelSword(Dice::rollRange(6, 10), 16);
    }

    protected function makeAxe(): WeaponInterface
    {
        return new SteelAxe(Dice::rollRange(8, 14), 12);
    }
}

class SilverWeaponGameApplication implements WeaponFactoryInterface
{
    protected function makeSword(): WeaponInterface
    {
        return new SilverSword(Dice::rollRange(4, 8), 12);
    }

    protected function makeAxe(): WeaponInterface
    {
        return new SilverAxe(Dice::rollRange(6, 12), 8);
    }
}
```

We're not going to cover this variant in the tutorial but we should at least mention it. In this
approach the logic for instantiating objects relies on the class that needs them. This is a simpler
approach but you lose reusability, and you depend on inheritance because the way to change what
objects will be created is by adding more derivatives of the abstract base class.

## Coding Example - Weapon Factory

We'll refactor how `AttackType` objects are created in the system, currently there are
two places where we use them, in the `CharacterBuilder` and `GameInfoCommand` classes.

This is how it looks like now:

```php
class CharacterBuilder
{
    private function createAttackType(string $attackType): AttackType
    {
        return match ($attackType) {
            'bow' => new BowType(),
            'fire_bolt' => new FireBoltType(),
            'sword' => new TwoHandedSwordType(),
            default => throw new \RuntimeException('Invalid attack type given')
        };
    }
}
```

If we'd need to add another type or change the constructor arguments, we'd need to find and
update all places where we instantiate them. It would be a time-consuming and error-prone task
to do in a real app. Also, it is not possible to swap what objects are
created dynamically (based on some config, input, or game event) in case you have different sets
of `AttackType` objects.

So, first step is to create a factory class. In this case we'll go with the "constructor argument" approach
because we already know that the make method will be called dynamically (from the user's input)

Copy-paste the code from `CharacterBuilder::createAttackType()` into a new class `AttackTypeFactory`

```php
class AttackTypeFactory
{
    public function create(string $type): AttackType
    {
        return match ($type) {
            'bow' => new BowType(),
            'fire_bolt' => new FireBoltType(),
            'sword' => new TwoHandedSwordType(),
            default => throw new \RuntimeException('Invalid attack type given')
        };
    }
}
```

Then, inject this factory into the `CharacterBuilder` as a dependency, and replace the `new` calls

```php
class CharacterBuilder
{
    public function __construct(private readonly AttackTypeFactory $attackTypeFactory)
    {
    }
    
    public function buildCharacter(): Character
    {
        $attackTypes = array_map(fn(string $attackType) => $this->attackTypeFactory->create($attackType), $this->attackTypes);
        
        ...
    }
}
```

Let's play a round and see if it works.

Great, celebrate by getting rid of the `createAttackType()` method, and imports.

And now we can do the same to the `GameInfoCommand` class. Inject the factory and replace the `new` calls

```php
class GameInfoCommand extends Command
{
    public function __construct(private readonly AttackTypeFactory $attackTypeFactory)
    {
        parent::__construct();
    }

    private function computeAverageDamage(string $attackTypeString, int $baseDamage = 0): float
    {
        $attackType = $this->attackTypeFactory->create($attackTypeString);
        ...
    }
}
```

## Coding Example - Multiple Factories *factories grouped by families*

To leverage this pattern to its maximum potential, we can introduce a `FactoryInterface` to
be able to swap factories dynamically. This is useful when we need to create different families of objects,
but the callers don't know anything about them.
For example, we could say that we want to have weapon levels based on the material they are made of.
e.g. Steel, Silver, Diamond, etc.

In our case we want to add a cheat code that allows the player to get a super powerful weapon. For this
we need to create another factory `UltimateWeaponFactory` and a `AttackTypeFactoryInterface` so we can
swap the factory at runtime.

```php
interface AttackTypeFactoryInterface
{
    public function create(string $type): AttackType;
}

class UltimateAttackTypeFactory implements AttackTypeFactoryInterface
{
    public function create(string $type): AttackType
    {
        return match ($type) {
            'bow' => new TitaniumBowType(),
            'fire_bolt' => new MeteorType(),
            'sword' => new SkullBreakerSwordType(),
            default => throw new \RuntimeException('Invalid attack type given')
        };
    }
}
```

Add them to the repository
Those classes will live in the `tutorial` directory so we can just copy-paste them

Don't forget to implement the interface in `AttackTypeFactory`

```php
class AttackTypeFactory implements AttackTypeFactoryInterface
{
}
```

then, we need to add a setter method to the builder so we can swap factories at runtime.
Also update the constructor so we can swap factories.

```php
class CharacterBuilder
{
    public function __construct(private AttackTypeFactoryInterface $attackTypeFactory)
    {
    }
    
    public function setAttackTypeFactory(AttackTypeFactoryInterface $attackTypeFactory): void
    {
        $this->attackTypeFactory = $attackTypeFactory;
    }
}
```

Rewrite to Attributes
And add an alias to the interface in the `services.yaml` file so we use the "normal" factory by default

```yaml
services:
    App\Factory\AttackTypeFactoryInterface: '@App\Factory\AttackTypeFactory'
```

Lastly, we need to add a new option to the `GameCommand` so we can activate the cheat code

```php
class GameCommand extends Command
    protected function configure()
    {
        $this->addOption('cheatCode', 'cc', InputOption::VALUE_OPTIONAL, 'You should not see this...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ...
        if ($input->getOption('cheatCode')) {
            $this->game->activateCheatCode($input->getOption('cheatCode'));
        }
        ...
    }
```

And now let's create that `activateCheatCode()` method in `GameApplication`

```php
class GameApplication
{
    public function activateCheatCode(string $cheatCode): void
    {
        switch ($cheatCode) {
            // Famous Konami Code
            case 'up-up-down-down-left-right-left-right-b-a-start':
                $this->characterBuilder->setAttackTypeFactory(new UltimateAttackTypeFactory());
                break;
            default:
                // print invalid cheat code
                break;
        }
    }
}
```

With this setup we can now introduce new sets of `AttackType` objects by just adding more
factories. Changing the implementation of and object at runtime can be achieved with any
other service that implements an interface, but when it is applied in addition to the factory pattern
you unlock another degree of flexibility because you're not just changing the behavior of the service,
you're also changing the objects that it creates.

## Using Factories with Symfony
https://symfony.com/doc/current/service_container/factories.html

We can quickly cover how to use factories in Symfony. Perhaps just talk about a couple of the use cases
and mention the others pointing to the documentation.

## Where do we see this in the real world?

- Form Component: uses factories to create form types, form fields, and form data transformers.
  the `FormFactory` class provides methods to create forms, which can be customized with different types and options.
  It uses factories and builders in conjunction to create and configure form objects.

## Conclusion
Factory pattern is a great way to decouple the code that creates objects from the code that uses them.
It centralizes the logic for creating objects in a single place, and it allows you to swap the implementation
of the objects at runtime. It is also a great pattern to use when you need to create different families of objects