# The Decorator Pattern

One more design pattern to go! And honestly, I think we may have saved the best for
last. It's the *decorator* pattern. This pattern is a *structural* pattern, so it's
all about how you organize and connect related classes. That will make more sense
as we uncover it.

## Definition

Here's the technical definition:

> The decorator pattern allows you to attach new behaviors to objects by placing
> these objects inside special *wrapper* objects that contain the behaviors.

Yeah... Let's try this definition instead:

> The decorator pattern is like an intentional man in the middle attack. You replace
> a class with your *custom* implementation, runs some code, then call the true method.

Before we get any deeper and nerdier, let's see it in action.

## The Goal

Here's the goal: I want to print something onto the screen whenever a player levels
up. The logic for leveling up lives inside of `XpCalculator`. But instead of changing
the code in *this* class, we're going to apply the decorator pattern, which will
allow us to run code before or *after* this logic... without actually *changing*
the code inside.

This is a particularly common pattern to leverage if the class you want to modify
is a *vendor* service that... you *can't* actually change. And *especially* if
that class doesn't give us any *other* way to hook into it, like by implementing
the observer or strategy patterns.

## Adding the Interface to Support Decoration

For the decorator pattern to work, there's just one rule: the class that we want
to decorate (meaning the class we want to extend or modify - `XpCalculator` in our
case) needs to implement an interface. You'll see why in a few minutes. If
`XpCalculator` were a *vendor* package, we... would just have to hope they did a
good job and made it implement an interface.

But since this is *our* code, we can add one. In the `Service/` directory, create
a new class... but change it to an interface. Let's call it `XpCalculatorInterface`.
Then, I'll go steal the method signature for `addXp()`, paste that here, add a `use`
statement and a semicolon. Easy enough!

Over in `XpCalculator`, implement `XpCalculatorInterface()`. And finally, open up
`XpEarnedObserver`. This is the *one* place in our code that *uses* `XpCalculator`.
Change this to allow *any* `XpCalculatorInterface`.

And this shows us *why* a class needs an interface to support decoration. Because
we're type-hinting the *interface*, we're going to be able to swap out the *true*
`XpCalculator` for our own implementation, known as the *decorator*. Let's create
that decorator class!

## Creating the Decorator

In the `src/Service/` directory, add a new PHP class and call it, how about,
`OutputtingXpCalculator`, since it's an `XpCalculator` that *outputs* things to the
screen. The most important thing about the decorator class is that it *must* call
all of the *real* methods on the *real* service. Yup, we're literally going to
pass the *real* `XpCalculator` *into* this one so we can call methods on it.

Create a `public function __construct()` and accept a
`private readonly XpCalculatorInterface` called, how about, `$innerCalculator`.
Our `OutputtingXpCalculator` *also* needs to implement `XpCalculatorInterface` so
that we can pass it to things like our observer.

Go to Code-Generate and select "Implement Methods" to generate `addXp()`. I'll add
the missing `use` statement and... perfect!

As I mentioned, the most important thing the decorator must *always* do is call
that inner service in all of the public methods. In other words, say
`$this->addXp($winner, $enemyLevel)`... oh I mean `$this->innerCalculator->addXp()`.

## A Chain of Decorators

Much better! With decorators, you create a chain of objects. In this case, we
have two: the `OutputtingXpCalculator` which will then call into the true
`XpCalculator`. One of the benefits of decorators is that you could have as *many*
as you want: we could decorate our decorator to create *three* classes!

## Adding Custom Logic

Anyways, down here, we now have the ability to run code before *or* after we call
the inner service. So *before*, say `$beforeLevel = $winner->getLevel()` to store
the level *before* we add the XP. Then, below, add
`$afterLevel = $winner->getLevel()`. Finally, `if ($afterLevel > $beforeLevel)`,
we know that we just leveled up!

And *that* calls for a celebration: let's print some stuff! I'll say
`$output = new ConsoleOutput()`... which is just a cheap way to write console, and
then I'll paste in a few lines to output a nice message.

## Swapping in the Decorated Class into your App

Ok, our decorator class is done! But... how do we hook this up? What we want to do
is *replace* *all* instances of `XpCalculator` in our system with our *new*
`OutputtingXpCalculator`.

Let's do this manually first, without Symfony's fancy container stuff. There's only
one place in our code that uses `XpCalculator`: `XpEarnedObserver`. Open up
`src/Kernel.php` and temporarily comment-out the "subscribe" magic that we had from
earlier. I'm doing this because, for the moment, I want to *manually* instantiate
my `XpEarnedObserver` and *manually* subscribe that in `GameApplication`... *just*
so we can see how everything works.

Over in `src/Command/GameCommand`, let's put back our manual observer pattern
setup logic from earlier: `$xpCalculator = new XpCalculator()` and then
`$this->game->subscribe(new XpEarnedObserver()` passing `$xpCalculator`.

We're note using the decorator yet... but this *should* be enough to keep our
app working lke before. When we try the command:

```terminal-silent
php bin/console app:game:play
```

We win! And we got some XP, which means `XpEarnedObserver` is doing its job.

So how do we use the decorator? By sneakily replacing the *real* `XpCalculator` with
the fake one. Say `$xpCalculator = new OutputtingXpCalculator()`, and pass it
the original `$xpCalculator`.

That's *it*! Suddenly, even though it has no idea, `XpEarnedObserver` is being
passed our decorator service! How cool is that?

So let's start over. Run the game again and battle a few times. The new decorator
we added *should* print a special message the moment that we level up. I'll fight
one more time and... got it! We're now level *two*. It works!

If you're wondering why the message printed *before* the battle actually started...
that "might" be because these icons here are really just fancy decoration: the battle
finishes before those show up.

Okay, we have *successfully* created a decorator service. Awesome! But how could
we replace the core `XpCalculator` with the decorator inside of Symfony's
*container*? Let's find out *next*. Plus, we'll do something even *cooler*:
completely replace a *core* Symfony service with our *own* decorator.
