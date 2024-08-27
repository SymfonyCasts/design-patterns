# Factory Pattern

It is time for our last design pattern, the *Factory pattern*. Factory is a
creational design pattern that provides a way to hide the details of how your objects
are created from the code that uses them. Instead of directly instantiating objects
with `new`, you use a factory that decides which specific object to create based on
the input.

## Pattern Anatomy

The factory pattern is composed of five parts:

First, an *interface* of the products we want to create. For example let's say
we want to create weapons for our characters so we'd have a `WeaponInterface`.
Under this context, products would be weapons.

Second, the *concrete products* that implement the interface. Following the example,
we'd have classes like `Sword`, `Axe`, `Bow`, etc.

Third, the *factory interface*. This is optional, but it is useful when you need to
create families of products. 

Fourth, is the *concrete factory* that implements the factory interface in case
there's one. This class is responsible for creating products.

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
may come from the user's input, or from a request, or something else.

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
But, luckily that can be solved with a good test suite, or you can transform the string into an `enum`.
Another problem is that it's not easy to have different constructor arguments on each type.

## Abstract Factory

And the last variant we'll talk about is the "Abstract Factory". In this approach you have
multiple factories implementing the same interface, and each concrete factory creates a family of objects.
In our weapons example, we could group weapons based on the material they are made of,
like steel, silver, etc. and each factory would create weapons of *only* that material.

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
(this code block can appear on screen as soon as the abstract factory is mentioned)

Depending on the application you can choose what factory will be used based on some config, or
swap the factory at runtime based on some event. In our game, we could change the weapons
factory whenever the game level changes. That would make it more exciting!

## Creating an AttackType Factory

Alright! It's time to put the factory pattern in action. We'll start by creating
the simplest factory possible and then promote it into an *abstract factory*. In our
application we create `AttackType` objects in a couple of places. One place is the `CharacterBuilder`,
open it up and look for the `createAttackType()` method, look at the `match` statement,
we create `AttackType` objects based on some string. Now open `GammeInfoCommand`, at the bottom
we have the same `match` statement, this type of duplicate code is not ideal because
if we'd want to add a new `AttackType` or change the constructor arguments, we'd need to find
and update all the places where we instantiate them. In big applications, this would
be a time-consuming and error-prone task. 

Ok, let's do it better and refactor this code with a factory. Copy
this `match` statement code, then inside the `src/` directory create a folder named `Factory/`,
and inside that add a new PHP class, name it `AttackTypeFactory`. Good, now we need
a method to create `AttackType` objects, write `public function create()` with
a `string $type` argument, and it will return `AttackType` objects. Inside `create()` paste
the code and rename the variable to `$type`. 

What we've done may look insignificant, but we've accomplished a lot because we've encapsulated
how `AttackType` objects are created throughout our application, and if that's not
cool enough, we've set the foundation for handling families of `AttackTypes`.
More on that soon.

Ok, the next step is to inject the `AttackTypeFactory` into the `CharacterBuilder`.
Open it up, and at the top add a constructor with
an argument `private readonly AttackTypeFactory $attackTypeFactory`. Then, find
the `buildCharacter()` method, there's where we call `createAttackType()`.
I'll split it into multiple lines to make it more readable. And now,
replace `createAttackType()` with `$this->attackTypeFactory->create()`. 
Perfect! Let's do the same in `GameInfoCommand`. Open it up, and add a constructor,
I'll let PhpStorm to auto-generate it for me so it adds the `parent` call, then
inject the factory `private readonly AttackTypeFactory $attackTypeFactory`.
Lastly, scroll down and find the `computeAverageDamage()` method, there's where
we call `createAttackType()` and replace it with `$this->attackTypeFactory->create()`.
Perfect! We're ready to give it a try. Spin over to your terminal but this time
run the `GameInfoCommand`

```terminal
php bin/console app:game:info
```

Yes! This is great! We can see information about out character classes and their weapons.
Celebrate by removing the duplicated code from the `CharacterBuilder` and `GameInfoCommand`.

Ok! We've successfully implemented the factory pattern, but a very simple one. Let's
take it to the next level and handle families of `AttackTypes` with an *abstract factory*.
That's next!
