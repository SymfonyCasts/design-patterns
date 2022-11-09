# Decoration: Override Core Services & AsDecorator

In Symfony, decoration has a secret super-power: it allows us to customize nearly
*any* service inside of Symfony.

For example, imagine that there's a core Symfony service and you need to extend its
behavior with your own. How could you do that? Well, we *could* subclass the core
service... and reconfigure things so that Symfony's container uses *our* class
instead of the core one. That *might* work, but *this* is where decoration shines.

So, as a challenge, let's extend the behavior of Symfony's core `EventDispatcher`
service so that whenever an event is dispatched, we dump a debugging message.

## Investigating the Event Dispatcher

The ID of the service that we want to decorate is `event_dispatcher`. And, fortunately,
the this class *does* implement an interface. Over on GitHub... on the
`symfony/symfony` repository, hit "t" and open `EventDispatcher.php`.

And... yup! This implements `EventDispatcherInterface`. Decoration *will* work!

## Creating the Decorator Class

So let's go make our custom decorator class. I'll create a new `Decorator/`
directory...  and inside, a new PHP class called... how about
`DebugEventDispatcherDecorator`.

Step one, is always to implement the interface: `EventDispatcherInterface`... though
this is a little tricky because there are *three* of them! There's `Psr`, which
is the smallest.. one from `Contract`, and *this* one from `Component`. The one
from `Component` extends the one from `Contact`... which extends the one from `Psr`.

We actually the "biggest" one: the one from `Symfony/Component`. The *reason* is
that, if our `EventDispatcher` decorator is going to be passed around the system
in place of the real one, it needs to implement the *strongest* interface: the
interface that has the *most* methods on it.

Go to Code Generate - or "command" + "N" on a Mac - and select "Implement
Methods" to implement the *bunch* we needed. Whew... there we go!

The other thing we need to do is add a constructor where the *inner*
`EventDispatcherInterface` will be passed to us... and make that a property with
`private readonly`.

Perfect! Now that we have this, we need to *call* the inner dispatcher in *all* of
these methods. This part is simple.... but boring. Let's say
`$this->eventDispatcher->addListener($eventName, $listener, $priority)`.

We also need to check whether or not the method should return a value. We don't
need to return in this method... but there are methods down here that *do* have return
values, like `getListeners()`.

To avoid spending the next 3 months repeating what I just did 8 more times, I'm going
to delete all of this and paste in a finished version: you can copy this from the
code block on this page. We're simply calling the inner dispatcher in every method.

*Finally*, now that our decorator is doing all the things it *must* do, we can
add custom functionality. Right before the inner `dispatch()` method is called,
I'll paste in two `dump()` calls and also dump  `Dispatching event`, `$event::class`.

## AsDecorator: Making Symfony use OUR Service

Ok! Our decorator class is done! But, there are *many* places in Symfony that rely
on the service whose ID is `event_dispatcher`. So here's the million dollar question:
how can we replace *that* service with our *own* service... but still get the original
event dispatcher passed to *us*?

Whelp, Symfony has a feature built *specifically* for this and you're going to
love it! Go to the top of our decorator class and add a PHP 8 attribute called:
`#[AsDecorator()]` and pass the id of the service that we want to decorate:
`event_dispatcher`.

That's *it*. This says:

> Hey Symfony, please make *me* the *real* `event_dispatcher` service, but still
> autowire the original `event_dispatcher` service *into* me.

Let's try it! Run our app:

```terminal-silent
php bin/console app:game:play
```

And... it works! Look! You can see the event being dumped out! And there's our
custom event too. And if I exit... another event at the bottom! We just replaced
the core `event_dispatcher` service with our *own* by creating a *single* class.
That's bananas!

## Using AsDecorator with OutputtingXpCalculator

So could we have used this `AsDecorator` trick earlier for our own `XpCalculator`
decoration situation? Yep! Here's how: In `config/services.yaml`, remove the manual
arguments and change the interface so it points to the original, undecorated service:
`XpCalculator`. Basically, in the service config, we want to set things up the
"normal" way, as if there were *no* decorators.

If we tried our app now, it *would* work, but it wouldn't be using our decorator.
But *now*, go into `OutputtingXpCalculator` add `#[AsDecorator()]` and pass it
`XpCalculatorInterface::class`, since that's the ID of the service we want to
decorate.

Donezo! If we try this now:

```terminal-silent
php bin/console app:game:play
```

*No errors*. An even faster way to prove this is working is by running:

```terminal
php bin/console debug:container XpCalculatorInterface --show-arguments
```

When we run this... check it out! It says that this is an *alias* for the service
`OutputtingXpCalculator`. So anyone that's autowiring this is actually getting
`OutputtingXpCalculator` service. And if you look down here at the arguments, the
first argument passed to `OutputtingXpCalculator` is the *real* `XpCalculator`.
That's amazing!

## Multiple Decoration

All right, the decorator pattern is *done*. What a cool pattern! One feature of the
decorator pattern that we only mentioned is that you can decorate a service as many
times as you want. Yep! If we created *another* class that implemented
`XpCalculatorInterface` and gave it this `AsDecorator` attribute, there would now
be *two* services decorating it. Which service would be on the outside? If you care
enough, you could set a `priority` option on one of the attributes to control it.

The biggest limitation to the decorator pattern is simply that you can only run
code before or *after* a method. We can't, for example, get into the *guts* of
any of the core `EventDispatcher` methods and change their behavior. Decoration
has *limited* power.

## Decoration in the Wild?

Where do we see decoration in the wild? The answer to that is... sort of all over.
In API Platform, it's very common to use decoration to extend core behaviors like
the ContextBuilder. And Symfony *itself* uses decoration pretty commonly to add
debugging features while we're in the `dev` environment. For example, we know that
this `EventDispatcher` class would be used in the `prod` environment. But in the
*dev* environment - I'll hit "T" to search for a "TraceableEventDispatcher" - assuming
that you have some debugging tools installed, *this* is the actual class that
represents the `event_dispatcher`. It *decorates* the real one!

I can prove it. Head back to your terminal and run:

```terminal
./bin/console debug:container event_dispatcher --show-arguments
```

Scroll to the top and... check it out! The `event_dispatcher` service is an *alias*
to `debug.event_dispatcher`... whose class is `TraceableEventDispatcher`! And if
you scroll down to its arguments, ha! It's passed our `DebugEventDispatcherDecorator`.
as an argument. Yup, there are *3* event dispatchers in this case: Symfony's
core `TraceableEventDispatcher` is on the outside, it calls into our
`DebugEventDispatcherDecorator`... and then *that* ultimately calls the real
event dispatcher. Inception!

## Problems Solved by Decorator

And what problems does the decorator pattern solve? Simple: it allows us to extend
the behavior of an *existing* class - like `XpCalculator` - even if that class
does *not* contain any other extension points. This means we can use it to override
vendor services when all else fails. The only downside to the decorator pattern is
that we can only run code *before* or *after* the core method. And the service we
want to decorate *must* implement an interface.

Okay, team. We're *done*! There are *many* more patterns out there in the wild:
this was a collection of some of our favorites. If we skipped one or several that
you really want to hear about, let us know! Until then, see if you can spot these
patterns in the wild and figure out where *you* can apply them to clean up your own
code... and impress your friends.

Thanks for coding with me, and I'll see you next time!
