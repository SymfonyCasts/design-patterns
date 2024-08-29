# Factory Pattern

It is time for our *last* design pattern - the *Factory pattern*. Factory is a *creational* design pattern that provides a way to *hide* the details of how your objects are created from the code that *uses* them. Instead of *directly* instantiating objects with `new`, we use a *factory* that decides which object to create based on the input.

## Pattern Anatomy

The Factory pattern is composed of five parts:

The first part is an *interface* of the products we want to create. If we wanted to create weapons for our characters, for example, we'd have a `WeaponInterface`, and the *products* would be weapons.

*Second* are the *concrete products* that implement the interface. In *this* example, we would have classes like `Sword`, `Axe`, `Bow`, and so on.

*Third* is the *factory interface*. This is *optional*, but it's super useful when you need to create *families* of products. 

*Fourth* is the *concrete factory* that implements the factory interface if we have one. This class is responsible for creating products.

And lastly, we have the *client*, which uses a factory to create *product objects*. This class only knows how to *use* products, but not how they're *created*, *or* what specific product it happens to be using.

## Factory with Multiple Make Methods

You may be surprised to learn that there's more than one variant of the Factory pattern. The *simplest* variant is a factory with multiple `make` methods - one for each possible product. That factory would look like this:

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

This variant is useful when the caller *already* knows which object it needs. It's also easy to have different constructor arguments for each type.

## Factory with a Single Make Method

Another approach is to use a *single* `make` method. That will receive an argument and determine which object it needs to create. This is useful when the application is more *dynamic*. The `$type` value may come from the user's *input*, a *request*, or something else.

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

*However*, there *are* a couple of downsides to this approach, like losing *type safety*, since *any* string can be sent as the type. Luckily, that can be solved with a good test suite, *or* by transforming the string into an `enum`. It's also hard to have different constructor arguments on each type.

## Abstract Factory

The *last* variant we'll talk about is the "Abstract Factory". In this approach, we have *multiple* factories implementing the *same* interface, and each concrete factory creates a family of objects. In our character weapons example, we could group weapons based on the *material* they are made of, like iron or steel, and each factory would *only* create weapons with that material.

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

Depending on the application, we can choose which factory will be used based on some config, or *swap* the factory at runtime based on some event. In our game, we could change the weapons factory whenever the game level changes, which would *definitely* make things more exciting.

## Creating an AttackType Factory

All right! It's time to see the Factory pattern in action! We'll start by creating the *simplest* factory possible, and then we'll *promote* it to an *abstract* factory. We've already created `AttackType` objects in a few places in our application. One of them is in the `CharacterBuilder`. Open that up and find the `createAttackType()` method. If we look at the `match` statement, we see that we're creating `AttackType` objects based on a string. If we open `GammeInfoCommand.php`, at the bottom... we have the *same* `match` statement. This duplicate code isn't *super* ideal because if we ever want to add a *new* `AttackType` or change the constructor arguments, we would have to find and update *all* of the places we instantiate them. In larger applications, this process would be error-prone and take a *ton* of time.

Surely there's a better way, right? There *is*! We're going to refactor this code with a *factory*. Copy this `match` statement code, and inside the `src/` directory, create a folder called `Factory/`. Inside *that*, add a new PHP class, and we'll call it `AttackTypeFactory`. Awesome! Now we need a method that will create `AttackType` objects. Write `public function create()`, give it a `string $type` argument, and make it return `AttackType` objects. In `create()`, paste the code and rename the variable to `$type`. 

Okay, what we've done so far may *seem* insignificant, but we've accomplished *a lot*. We just defined *how* `AttackType` objects are created *throughout* our application and, as a bonus, we also set the foundation for handling *families* of `AttackTypes`. We'll talk about that more later on.

The *next* step is to inject the `AttackTypeFactory` into the `CharacterBuilder`. Open that up and, at the top, add a constructor with an argument - `private readonly AttackTypeFactory $attackTypeFactory`. Then, find the `buildCharacter()` method. That's where we call `createAttackType()`. I'll split that onto multiple lines so it's easier to read. And now, replace `createAttackType()` with `$this->attackTypeFactory->create()`. *Perfect*! Let's do the same thing in `GameInfoCommand.php`. Open that... and add a constructor. We can let PhpStorm auto-generate that for us so it automatically adds the `parent` call. *Then* we'll inject the *factory* - `private readonly AttackTypeFactory $attackTypeFactory`. *Finally*, scroll down, find the `computeAverageDamage()` method... and once again, replace `createAttackType()` with `$this->attackTypeFactory->create()`. Awesome!

I think we're ready to give this a try! Spin over to your terminal and, *this time*, run the `GameInfoCommand`:

```terminal
php bin/console app:game:info
```

And... *yes*! This is great! We can see information about out character classes *and* their weapons. Let's celebrate by removing all of the duplicated code from `CharacterBuilder` and `GameInfoCommand`.

All right! We've *successfully* implemented the Factory pattern! This was the *simplest* variation of the pattern, so *next*, let's kick things up a notch and handle *families* of `AttackTypes` with an *abstract factory*.
