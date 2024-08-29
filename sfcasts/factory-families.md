# The Abstract Factory Pattern

Let's level up our `AttackTypeFactory` and make it an *abstract factory*. As we've
seen in the previous chapter, an abstract factory allows you to handle families
of objects. For this purpose, we'll introduce *cheat codes* to the game... yes,
it's time to play dirty! So, if it's activated, we'll create the most powerful
weapons in the game! To do this, we'll need to create another factory and a way
to swap it at runtime.

## Adding More Factories

Ok! The first thing we need to do is to create an interface for our factories. Instead
of doing it manually I'll show you a little trick and let PhpStorm do it for you. Open up `AttackTypeFactory`,
right-click on the class name, select "Refactor" and then "Extract Interface". Change
the name to `AttackTypeFactoryInterface` and click on "Refactor". Hey, look at this! there's
our interface, and it has a `create()` method just like we wanted, and in `AttackTypeFactory`
it's already implemented. This is handy, isn't it?

The next step is to create a new factory for the new "set" or "family" of `AttackType` objects. 
Inside the `Factory/` directory add a new PHP class and call it `UltimateAttackTypeFactory`.
Implement the interface, and add the `create()` method by holding "Option" + "Enter"
and select "add stubs methods". Let me clean this up, perfect! Now, for the `create()`
method we'll write a quite similar `match` statement to the one in `AttackTypeFactory`,
so write `return match ($type)`, and inside we'll have the same cases but returning
different `AttackType` objects. For example, in the `bow` case we'll return a more
powerful weapon, a `TitaniumBowType` object. But wait, that class does not exist yet.
To save us some time, those new `AttackType` classes are already prepared in the `tutorial/`
directory at the root of the project. Open it up , copy the `Ultimate/` folder
and paste it inside `src/AttackType/`. Good, let's finish this up. Add a `sword` case
and return `new SkullBreakerSwordType()`. For the `fire_bolt` case,
return `new MeteorType()`, and lastly the `default` case, just throw a `\RuntimeException()`
with a message `Invalid attack type`.

The next step is to add a way to *swap* our factories at runtime. Open up `CharacterBuilder`
and scroll up to its constructor, we can see it has a dependency to the
concrete `AttackTypeFactory` class. We need it to work with any factory, so change
its type hint to `AttackTypeFactoryInterface`. Then, to make it interchangeable, we
need a setter for this property, so remove the `readonly` statement, then add the
setter method by moving the cursor to the property name, hold "Option" + "Enter"
and select "Add Setter".

## Adding Cheat Codes

Ok, we're getting closer! Next, we need a way to play *cheat codes*. We'll handle them
as command-line options. So, open up `GameCommand` and below the constructor
write `protected function configure()`. Inside, add a new option by calling
`$this->addOption()`. The first argument is the option's name, call it `cheatCode`.
The second argument is the shortcut, let's use `c`, and for the third argument
this is going to be an optional value, so set it to `InputOption::VALUE_OPTIONAL`.
Then, inside the `execute()` method, before selecting the character, we'll check
if the cheat code option was passed in, and if so we'll activate it. To do this,
write `if ($input->getOption('cheatCode'))`, and inside, write 
`$this->game->activateCheatCode()` sending the cheat code option as argument. This
method does not exist yet so let's create it. Position the cursor over the method's name,
hold "Option" + "Enter" and select "Add Method". Ok, change the argument to `string $cheatCode`,
inside we'll use a `switch-case` just in case we decide to add more cheat codes in the future.
So write `switch ($cheatCode)` and inside we'll have a `case` with the value of our
*ultimate* cheat code. Hm, what would be a good value for it? Oh, I know the perfect value!
I'll paste it because it's a bit long. Do you remember it? It's the famous Konami code!
I feel like the 90s are back! Ok, inside the `case` print a message so we know that
the cheat code was activated, and then swap the factory on the `CharacterBuilder`.
To do this, write `$this->characterBuilder->setAttackTypeFactory(new UltimateAttackTypeFactory())`.
and `break` at the end. I know what you're thinking... what if the `UltimateAttackTypeFactory`
may have dependencies or is just not that simple to instantiate? Well, that's a valid concern.
A way to solve this is doing the same thing we did with the "state" classes, by leveraging
the [`AutowireLocator` attribute](https://bit.ly/sf-service-locator-attribute). Another
option would be to create a factory for your factories... Hm, I hope Skynet is not listening!
Ok, finish this up by adding a `default` case and print an `Invalid Cheat Code` message.

Perfect! Before giving this a try, there's a tiny detail we need to handle. Symfony
does not know what `AttackTypeFactory` to inject into `CharacterBuilder` because we 
have more than one implementation of the `AttackTypeFactoryInterface` so we need to
tell Symfony which one to use by default. To do this I'll leverage the `AsAlias` attribute,
open up `AttackTypeFactory` and above the class name write `#[AsAlias()]` and pass
`AttackTypeFactoryInterface::class` as the id.

Ok, finally! Let's give this a try. Spin over to your terminal and run:

```terminal
php bin/console app:game:play -c up-up-down-down-left-right-left-right-b-a-start
```

And, don't forget to pass the cheat code. Oh, look! There's our `cheat code activated` message.
Ok, I'll play a battle and... we won in just two rounds! Cheat codes for the win!

Alright, let's see how the *factory pattern* is used in the real world. That's next!
