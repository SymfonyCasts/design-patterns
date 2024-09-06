# The Abstract Factory Pattern

Let's level up our `AttackTypeFactory` and make it an *abstract factory*. As we
saw in the previous chapter, an abstract factory allows us to handle *families*
of objects. To illustrate this, we're introducing *cheat codes* to the game
that, if activated, will give us some *super* powerful weapons. *Oh yeah*, now
we can *really* rule the game! To add cheat codes, we'll need to create
*another* factory and a way to swap it at runtime. So let's get going!

## Adding More Factories

The first thing we need to do is create an interface for our factories. Instead
of doing it *manually*, I'll show you a little trick that will have PhpStorm do
it *for* you. Open up `AttackTypeFactory`, right click on the class name,
select "Refactor" and then "Extract Interface". Change the name to
`AttackTypeFactoryInterface` and click on "Refactor" again. Hey! Look at that!
*There's* our interface, and it has a `create()` method just like we wanted. And
over in `AttackTypeFactory`, it's *already implemented*. Pretty handy, right?

The next step is to create a new factory for the new "set" or "family" of
`AttackType` objects. Inside the `Factory/` directory, add a new PHP class that
we'll call `UltimateAttackTypeFactory`. Implement the interface... and add the
`create()` method by holding "Option" + "Enter" and selecting "Add method
stubs". I'll clean this up, and... perfect!

Now, for the `create()` method, we'll add a `match` statement that's very
similar to the one in `AttackTypeFactory`. Write `return match ($type)` and,
inside, we'll use the same cases but return different `AttackType` objects. For
example, in the `bow` case, we'll return a *more powerful* weapon - a
`TitaniumBowType` object. But... wait. That class doesn't exist yet, right?
Nope! But to save us some time, those new `AttackType` classes are *already*
prepared in the `tutorial/` directory at the root of our project. Open that up,
copy the `Ultimate/` folder, and *paste* it inside `src/AttackType/`.

Okay! Let's finish this up! Add a `sword` case and return
`new SkullBreakerSwordType()`. For the `fire_bolt` case, return
`new MeteorType()`. And lastly, for the `default` case, just throw a
`\RuntimeException()` with a message - `Invalid attack type`. Done!

Next, we need to add a way to *swap* our factories at runtime. Open
`CharacterBuilder` and scroll up to its constructor. We can see that this
already has a dependency to the concrete `AttackTypeFactory` class. We need it
to work with *any* factory, so change its type hint to
`AttackTypeFactoryInterface`. Then, to make it *interchangeable*, we'll need a
setter for this property, so remove the `readonly` statement and add the setter
method by moving the cursor over the property name, holding "Option" + "Enter" and
selecting "Add Setter".

## Adding Cheat Codes

All right! We're getting closer! Now we need a way to *play* our cheat codes.
We'll handle them as command-line options, so open up `GameCommand` and,
below the constructor, write `protected function configure()`. Inside, add a new
option by calling `$this->addOption()`. The *first* argument is the option's
*name*. We'll call it `cheatCode`. The *second* argument is the *shortcut*.
Let's use `c`. The *third* argument is going to be an *optional* value, so let's
set it to `InputOption::VALUE_OPTIONAL`. Then, inside the `execute()` method,
before selecting the character, we'll check to see if the `cheatCode` option was
passed in, and if *so*, we'll activate it.

To do this, write `if ($input->getOption('cheatCode'))`, and inside *that*,
`$this->game->activateCheatCode()`, sending the `cheatCode` option as the
argument. This method doesn't exist yet, so let's create it. Position the cursor
over the method's name, hold "Option" + "Enter", and select "Add method". Okay,
change the argument to `string $cheatCode`... and inside, we'll use a
`switch-case` just in case we want to add more cheat codes in the future. To do
that, say `switch ($cheatCode)` and inside that, we'll add a `case` with the
value of our *ultimate* cheat code. Hm... what would be a good value for that?
Oh! I know! I'll paste this in because it's a bit long, but it *might* look
familiar to you. Remember the famous Konami code? It feels like the 90s are
back!

Okay, *inside* the `case`, let's print a message so we know that the cheat code
was activated. *Then* we'll swap the factory on the `CharacterBuilder`. To do
that, write
`$this->characterBuilder->setAttackTypeFactory(new UltimateAttackTypeFactory())`
and add a `break` at the end. *Awesome*.

Now, you may be thinking "What if the `UltimateAttackTypeFactory` has
dependencies?" or "What if it's not that simple to instantiate?", and that's a
valid concern. A way to solve this is the same as what we discussed with
"state" classes - by leveraging the [`AutowireLocator` attribute](https://bit.ly/sf-service-locator-attribute).
Another option would be to create a factory *for* your factories.
Ohh factory-ception! I sure hope Skynet isn't listening... Ok, we can finish this
up by adding a `default` case and print an `Invalid Cheat Code` message. Perfect!

Before we give this a try, there's a *tiny* detail we need to handle. Symfony
doesn't know which `AttackTypeFactory` to inject into `CharacterBuilder` because
we have more than one implementation of the `AttackTypeFactoryInterface`. We
need to tell Symfony which one to use by default. To do that, we can leverage
the `AsAlias` attribute. Open `AttackTypeFactory` and, above the class name,
write `#[AsAlias()]` and pass `AttackTypeFactoryInterface::class` as the ID.
Done!

Let's try it out! Spin over to your terminal and run:

```terminal
php bin/console app:game:play -c up-up-down-down-left-right-left-right-b-a-start
```

Hey! Look at that! There's our `Ultimate cheat code activated!` message. And if we
battle... we *won* in just *two rounds*! Cheat codes for the win!

Up next: Let's see how the *factory pattern* is used in the *real world*.
