# Factory Pattern

It is time for our last design pattern, the *Factory pattern*. Factory is a
creational design pattern that provides a way to hide the details of how your objects
are created from the code that uses them. Instead of directly instantiating objects
with `new`, you use a factory that decides which specific object to create based on
the input.

## Pattern Anatomy

The factory pattern is composed of five parts:

First is the *interface* of the products we want to create. For example let's say
we want to create weapons, so we'd have a `WeaponInterface`. In other words,
our products would be weapons.

Second is the *concrete products* that implement the interface. Following the example,
we'd have `Sword`, `Axe`, `Bow`, etc.

Third is the *factory interface*. This is optional, but it is useful when you need to
create families of products. 

Fourth is the *concrete factory* that implements the factory interface in case
there's one. This class is responsible for creating the objects.

And finally, the *client* that uses a factory to create product objects. This
class only knows how to use products but not how they are created or what specific
product it is using.

## Factory with Multiple Make Methods

There are a few variants of the factory pattern. The simplest one is a factory
with multiple `make` methods, one per each possible product. The factory would
look like this:

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

This variant is useful when the caller already knows what object it needs. Also,
is easy to have different constructor arguments per each type.

## Factory with a Single Make Method

Another approach is having a single `make` method that receives an argument that determines
what object to create. This is useful when the application is more dynamic, the `$type` value
can come from the user's input, or from the request, or something else.

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

But, it comes with a downside. You lose type safety because any string can be sent as the type.
Luckily that can be solved with a good test suite, or transform the string to an `enum`.
Another problem is that it's harder to have different constructor arguments for each type.

## Abstract Factory

Ok, the last variant we'll talk about is the "Abstract Factory". In this approach you have
multiple factories implementing the same interface, and each concrete factory creates a family of objects.
In our weapons example, we could group the weapons based on the material they are made of,
like steel, silver, etc. and each factory would create weapons of *only* that material.
Depending on the application you can choose what factory will be used based on some config, or
change it at runtime. In our game, we could change the weapons factory whenever
the game level changes, that would make it more exciting!

```php
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

## Creating a Weapon Factory

Alright! It's time to put it in action. In our application there are a couple of places
where we create `AttackType` objects. Take a look at the `CharacterBuilder`, look for the method, etc... we have thsi same `match` in `GameInfoCommand`.
So, if we add a new `AttackType` or change the constructor arguments we'd need to find
and update all places where we instantiate them. In a big application, this would
be a time-consuming and error-prone task. Let's do better and refactor this code with a factory.

We'll start by creating the simplest factory possible and then upgrade it into an *abstract factory*.
Ok, copy this `match` statement...

* show `CharacterBuilder` create method
* copy `match`
* create `AttackTypeFactory` class and paste
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

* inject factory into the `CharacterBuilder` and replace the `new` calls

```php
class CharacterBuilder
{
    public function __construct(private readonly AttackTypeFactory $attackTypeFactory)
    {
    }
    
    public function buildCharacter(): Character
    {
        $attackTypes = array_map(fn(string $attackType) => $this->attackTypeFactory->create($attackType), $this->attackTypes);        
    }
}
```

* do the same in `GameInfoCommand`

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
    }
}
```

* Run command `php bin/console app:game:info` and see if it works
* coming next: factories grouped by families of objects
