# Builder Pattern

Time for "design pattern" number *two*: the "builder pattern". This is one of those
creational patterns that help you instantiate and configure objects. And, it's a
bit easier to understand than the "strategy pattern".

## Official Definition

The *official* definition of the "builder patter" is this:

> A creational design pattern that lets you build and configure complex objects
> step-by-step.

That... actually made sense. Part *two* of the official definition says:

> the pattern allows you to produce different types and representations of an object
> using the same construction code.

In other words, the builder pattern is where you create a builder class that helps
you *build* other objects... which might be of *different* classes or the *same*
class with different data.

## Simple Example

A simple example might be a pizza parlor that needs to create a bunch of pizzas,
each which have a different crust, toppings, etc. To make life easier, the owner
of the pizza parlor, who is also a Symfony developer by night, decides to create
a `PizzaBuilder` class with easy methods like `addIngredient()`, `setDough()`, and
`addCheese()`. Then, they create a `buildPizza()` method, which takes *all* of that
info and does the heavy lifting of *creating* that `Pizza` object and returning it.
That `buildPizza()` method can be as complicated as needed. Anyone *using* this class
to doesn't know or care about any of that.

## Creating the Builder Class

Ok, let's create a builder in *our* app. If we head over to `GameApplication`, down
here in `createCharacter()`, the *problem* is that we're building *four* different
`Character` objects and passing *quite* a bit of data to configure each one.
And, suppose we need to create these `Character` objects in *other* places in our
code. They're not *super* easy to build right now. We *could* make some sub-class
of `Character` that can set this data up automatically, like by calling the parent
constructor. But, like we talked about with the strategy pattern, that could get
really ugly when we start having things like a `mage-archer` with an `IceBlockType`
shield class.

And what if creating a `Character` object were even *more* difficult? Like, if it
required making queries to the database or other operations? The goal here is to
make the instantiation of the `Character` objects easier and more clear. And we can
accomplish that by creating a builder class.

Add a `src/Builder/` directory for organization and, inside of that, create
a new PHP class called `CharacterBuilder`. I'm creating this class but I am
*not* creating a corresponding interface. Builder classes *often* implement an
interface like `CharacterBuilderInterface`, but they don't *need* to. Later, we'll
talk about why you *might* decide to add an interface in *some* situations.

## Methods and Method Chaining

Okay, inside, we get to create whatever methods we want so that the outside world
craft their new character. For example, `public function setMaxHealth()`, which will
accept an `int $maxHealth` argument. I'm going to leave this method empty for the
moment... but it well eventually return *itself*: it will return `CharacterBuilder`.
This is really common in the builder pattern because it allows method chaining.
But, it's not a *requirement* of the pattern.

All right, let's quickly fill in a few more methods, like `setBaseDamage()`... and
the last two things are the armor and attack types. So say `setAttackType()`.
Remember, attack types are *objects*. But instead of allowing an `AttackType`
interface argument, I'm going to accept a `string` argument called `$attackType`.
Why? I don't *have* to do it this way, but I'm trying to make it as *easy* as possible
to create characters. So instead of making *someone else* instantiate the attack
types, I'm going to allow them to pass a simple string - like the word `bow` - and,
in a few minutes, *we* will handle the complexity of instantiating the object for
them.

Okay, copy that, and do the same thing for `setArmorType()`.

And... that's it! Those are the only four things that you can control in a character.

## The Creational Maethod

The *final* method that our builder needs is the one that will actually *build*
the `Character`. You can call this anything you want, how about `buildCharacter()`.
And it is, of course, going to return a `Character` object.

To store the character preferences of the user calls methods on this class, we're
going to create *four* properties, which I'll just paste in: `private int $maxHealth`,
`private int $baseDamage`, and then `private string $attackType` and
`private string $armorType`. Then, in each method, *assign* that property and `return
$this`. We'll do that for `$baseDamage`... `$attackType`... and `$armorType`.

Beautiful! The `buildCharacter()` method is fairly straightforward: we do whatever
ugly work we need to do to create the character. So I'll say `return new Character()`.
The first two arguments are pretty simple: `$this->maxHealth` and `$this->baseDamage`.
The last two, which require objects, will be a bit more complex. But that's ok!
I don't mind if my builder gets a little complicated.

## Doing some Heavy Lifting

Go to the bottom of this class and paste in two new `private` methods. These
handle creating the `AttackType` and `ArmorType` object. Except... I need a bunch
of `use` statements for this, which I forgot. Whoops. So I'm going to re-type the
end of these classes and hit "tab" to add those `use` statements. There we go!

Okay, we can now use the two new `private` methods to map the strings to *objects*.
*This* is the heavy lifting - and the real value - of our `CharacterBuilder`. Say
`$this->createAttackType()` and `$this->createArmorType()`.

And our builder is done! Next: let's use this in `GameApplication`. Then, we'll make
our builder even *more* flexible (but not more difficult to use) by accounting for
characters that use *multiple* attack types.
