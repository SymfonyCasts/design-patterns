# Observer Inside Symfony + Benefits

We've implemented the Observer Pattern! The `GameApplication` is our subject, which
notifies all of the observers... and we have *one* at the moment:
`XpEarnedObserver`. Inside `GameCommand`, we connected all of this by *manually*
instantiating the observer and `XpCalculator`... then calling
`$this->game->subscribe()`:

[[[ code('b81246b495') ]]]

But... that isn't very Symfony-like.

Both `XpEarnedObserver` and `XpCalculator` are *services*. So we would *normally*
autowire them from the container, not instantiate them manually. We *are* autowiring
`GameApplication`... but our overall situation isn't quite right. In a perfect world,
by the time Symfony gives us this `GameApplication`, Symfony's container would
have *already* hooked up all of its observers so that it's ready to use
*immediately*. How can we do that? Let's do it the simple way first.

## Manually Specifying the Services

Remove all of the manual code inside of `GameCommand`:

[[[ code('b393cb46bc') ]]]

We're going to recreate this same setup... but inside `services.yaml`. Open that...
and at the bottom, we need to modify the service `App\GameApplication`.
But we don't need to configure any arguments. In this case, we need to configure
some `calls`. Here, I'm basically telling Symfony:

> Yo! After you instantiate `GameApplication`, call the `subscribe()` method on
> it and pass, as an argument, the `@App\Observer\XpEarnedObserver` service.

[[[ code('9680bb773e') ]]]

So when we autowire `GameApplication`, Symfony will go grab the `XpEarnedObserver`
service and *that* service will, of course, get `XpCalculator` autowired into *it*.
This is pretty normal autowiring: the only special part is that Symfony will
now call the `subscribe()` method on `GameApplication` before it passes that
object to `GameCommand`.

In other words, this *should* work. Let's give it a try! Run:

```terminal
./bin/console app:game:play
```

There are no errors so far and... oh. We lost. Bad luck. Let's try again! We won
*and* we received 30 XP. It's working!

## Setting up Autoconfiguration

The downside to this solution is that every time we add a new observer, we'll need
to go to `services.yaml` and wire it *manually*. *Gasp*, how *undignified*...

Could we *automatically* subscribe all services that implement
`GameObserverInterface`? Why, yes! And what an *excellent* idea! We can do that in
two steps.

First, open `src/Kernel.php`. This isn't a file we work with much, but we're
about to do some deeper things with the container and so this is exactly where we
want to be. Go to Code Generate or `Command`+`O` and select "Override Methods".
We're going to override one called `build()`:

[[[ code('9dbbdee6d4') ]]]

Perfect! The parent method is empty, so we don't need to call it at all. Instead,
say `$container->registerForAutoconfiguration()`, pass it
`GameObserverInterface::class`, and then say `->addTag()`. I'm going to invent a
new tag here called `game.observer`:

[[[ code('b21b3d58b0') ]]]

This probably isn't something you see very often (or ever) in *your* code, but it's
really common in third-party bundles. This says that any service that implements
`GameObserverInterface` should automatically be given this `game.observer` tag...
assuming that service has `autoconfigure` enabled, which all of our services do.

That tag name could be *any* string... and it doesn't do anything at the moment:
it's just a random string that's now attached to our service.

But we *should*, at least, be able to see it. Spin over and run:

```terminal
./bin/console debug:container xpearnedobserver
```

It found our service! And check it out: `Tags` - `game.observer`.

Ok, now that our service has a *tag*, we're going to write a little more code
that automatically calls the `subscribe` method on `GameApplication` for *every*
service *with* that tag. This is *also* going to go in `Kernel`, but in a
 *different* method. In this case, we're going to implement something called a
 "compiler pass".

Add a new interface called `CompilerPassInterface`. Then, below, go back to
"Code Generate", "Implement Methods", and select `process()`:

[[[ code('c2111a0bce') ]]]

Compiler passes are a bit more advanced, but super cool! It's a piece of code that
runs at the *very* end of the container and services being built... and you can do
*whatever* you want inside.

Check it out! Say `$definition = $container->findDefinition(GameApplication::class)`:

[[[ code('edee7fe331') ]]]

No, this does *not* return the `GameApplication` object. It returns a `Definition`
object that knows everything about *how* to instantiate a `GameApplication`, like
its class, constructor arguments, and any calls it might have on it.

Next, say `$taggedObservers = $container->findTaggedServiceIds('game.observer')`:

[[[ code('d7559f373c') ]]]

This will return an array of all the services that have the `game.observer` tag.
Then we can loop over them with `foreach ($taggedObservers as $id => $tags)`. The
`$id` is the service id... and `$tags` is an array because you can technically
put the same tag on a service multiple times... but that's not something we care
about:

[[[ code('e78648b174') ]]]

Now say `$definition->addMethodCall()`, which is the PHP version of `calls` in YAML.
Pass this the `subscribe` method and, for the arguments, a `new Reference()` (the
one from `DependencyInjection`), with `id`:

[[[ code('5129f22d15') ]]]

This is a fancy way of saying that we want the `subscribe()` method to be called
on `GameApplication`... and for it to pass the *service* that holds the
`game.observer` tag.

The end result is the same as what we had before in `services.yaml`... just more
dynamic and better for impressing your programmer friends. So, remove all of
the YAML code we added:

[[[ code('48a9a1bae1') ]]]

If we try our game again...

```terminal
./bin/console app:game:play
```

No errors! And... yes! It *still* works! If we need to add another observer later,
we can just create a class, make it implement `GameObserverInterface` and... done! It
will *automatically* be subscribed to `GameApplication`.

## Observer Pattern in the Wild

So *that* is the observer pattern. How it looks can differ, with different method
names for subscribing. Heck, sometimes the observers are passed in through the
constructor! But the idea is *always* the same: a central object loops over and calls
a method on a collection of other objects when something happens.

Where do we see this in the wild? It shows up in a lot of places, but here's *one*
example. Over on Symfony's GitHub page, I'm going to hit "T" and search for a class
called `LocaleSwitcher`. If you need to do something in your application each time
the locale switches, you can register your code with the `LocaleSwitcher` and it will
call you. In this case, the observers are passed through the constructor. And then
you can see down here, after the locale is set, it loops over all of those and
calls `setLocale()`. So `LocaleSwitcher` is the subject, and these are the observers.

How do you register an observer? Not surprisingly, it's by creating a class that
implements `LocaleAwareInterface`. Thanks to autoconfiguration, Symfony will
automatically tag your service with `kernel.locale_aware`. Yup, it uses the same
mechanism for hooking all of this up that we just used!

## Benefits of the Observer Pattern

The *benefits* of the observer pattern are actually best described by looking at the
SOLID principles. This pattern helps the Single Responsibility pattern because you
can encapsulate (or isolate) code into smaller classes. Instead of putting everything
into `GameApplication`, like all of our XP logic right here, we were able to isolate
things in `XpEarnedObserver` and keep both classes more focused. This pattern
also helps with the Open-closed Principle, because we can now extend the behavior
of `GameApplication` *without* modifying its code.

The observer pattern also follows the Dependency Inversion Principle or DIP, which
is one of the trickier principles if you ask me. Anyways, DIP is happy because
the high-level class - `GameApplication` - accepts an interface -
`GameObserverInterface` - and that interface was designed for the purpose of how
`GameApplication` will use it. From GameApplication's perspective, this interface
represents something that wants to "observe" what happens when something occurs
within the game. Namely, the fight finishing. And so, `GameObserverInterface`
is a good name.

But, if we had named it based on how the *observers* will *use* the interface, that
would have made DIP sad. For example, had we called it
`XpChangerInterface` and the method `timeToChangeTheXp`, *that* would be a violation
of the Dependency Inversion Principle. If that's fuzzy and you want to know more,
check out our SOLID tutorial.

Next, let's quickly turn to the brother pattern of observer: *Pub/sub*.
