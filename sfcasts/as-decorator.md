# Decoration: Override Core Services & AsDecorator

In Symfony, decoration has a secret super-power: it allows us to customize nearly
*any* service inside of Symfony. Woh.

For example, imagine that there's a core Symfony service and we need to extend its
behavior with our own. How could we do that? Well, we *could* subclass the core
service... and reconfigure things so that Symfony's container uses *our* class
instead of the core one. That *might* work... but *this* is where decoration shines.

So, as a challenge, let's extend the behavior of Symfony's core `EventDispatcher`
service so that whenever an event is dispatched, we dump a debugging message.

## Investigating the Event Dispatcher

The ID of the service that we want to decorate is `event_dispatcher`

```terminal-silent
php ./bin/console debug:contianer event_dispatcher
```

And, fortunately, this class *does* implement an interface. Over on GitHub...
on the `symfony/symfony` repository, hit `t` and open `EventDispatcher.php`.

And... yup! This implements `EventDispatcherInterface`. Decoration *will* work!

## Creating the Decorator Class

Let's go make our decorator class. I'll create a new `Decorator/` directory...
and inside, a new PHP class called... how about `DebugEventDispatcherDecorator`.

Step one, is always to implement the interface: `EventDispatcherInterface`... though
this is a little tricky because there are *three* of them! There's `Psr`, which
is the smallest... one from `Contract`, and *this* one from `Component`. The one
from `Component` extends the one from `Contract`... which extends the one from `Psr`.

Which do we want? The "biggest" one: the one from `Symfony\Component`:

[[[ code('ecb8362a8e') ]]]

The *reason* is that, if our `EventDispatcher` decorator is going to be passed
around the system in place of the *real* one, it needs to implement the *strongest*
interface: the interface that has the *most* methods on it.

Go to "Code"->"Generate" - or `Command`+`N` on a Mac - and select "Implement methods"
to add the *bunch* we needed. Whew... there we go!

[[[ code('5b54e6cbdc') ]]]

The other thing we need to do is add a constructor where the *inner*
`EventDispatcherInterface` will be passed to us... and make that a property with
`private readonly`:

[[[ code('ac9c749713') ]]]

Now that we have this, we need to *call* the inner dispatcher in *all* of
these methods. This part is simple.... but boring. Say
`$this->eventDispatcher->addListener($eventName, $listener, $priority)`:

[[[ code('ccee6d87b2') ]]]

We also need to check whether or not the method should return a value. We don't
need to return in this method... but there *are* methods down here that *do* have
return values, like `getListeners()`.

To avoid spending the next 3 minutes repeating what I just did 8 more times and
putting you to sleep... bam! I'll just paste in the finished version:

[[[ code('e5808aa772') ]]]

You can copy this from the code block on this page. We're simply calling
the inner dispatcher in every method.

*Finally*, now that our decorator is doing all the things it *must* do, we can
add our custom stuff. Right before the inner `dispatch()` method is called,
I'll paste in two `dump()` lines and also dump `Dispatching event`, `$event::class`:

[[[ code('3cd2ea3c23') ]]]

## AsDecorator: Making Symfony use OUR Service

Ok! Our decorator class is done! But, there are *many* places in Symfony that rely
on the service whose ID is `event_dispatcher`. So here's the million dollar question:
how can we replace *that* service with our *own* service... but still get the original
event dispatcher passed to *us*?

Whelp, Symfony has a feature built *specifically* for this and you're going to
love it! Go to the top of our decorator class, add a PHP 8 attribute called:
`#[AsDecorator()]` and pass the ID of the service that we want to decorate:
`event_dispatcher`:

[[[ code('5eb85a4815') ]]]

That's *it*. Seriously! This says:

> Hey Symfony! Thanks for being so cool! Also, please make *me* the *real*
> `event_dispatcher` service, but still autowire the *original* `event_dispatcher`
> service *into* me.

Let's try it! Run our app:

```terminal-silent
php ./bin/console app:game:play
```

And... it works! Look! You can see the event being dumped out! And there's our
custom event too. And when I exit... another event at the bottom! We just replaced
the core `event_dispatcher` service with our *own* by creating a *single* class.
That's bananas!

## Using AsDecorator with OutputtingXpCalculator

Could we have used this `AsDecorator` trick earlier for our own `XpCalculator`
decoration situation? Yep! Here's how: In `config/services.yaml`, remove the manual
arguments:

[[[ code('216b7061dd') ]]]

And change the interface to point to the original, undecorated service:
`XpCalculator`:

[[[ code('1171f2d272') ]]]

Basically, in the service config, we want to set things up the "normal" way,
as if there were *no* decorators.

If we tried our app now, it *would* work, but it wouldn't be using our decorator.
But *now*, go into `OutputtingXpCalculator` add `#[AsDecorator()]` and pass it
`XpCalculatorInterface::class`, since that's the ID of the service we want to
replace:

[[[ code('5216eb5087') ]]]

Donezo! If we try this now:

```terminal-silent
php ./bin/console app:game:play
```

*No errors*. An even faster way to prove this is working is by running:

```terminal
php ./bin/console debug:container XpCalculatorInterface --show-arguments
```

And... check it out! It says that this is an *alias* for the service
`OutputtingXpCalculator`. So anyone that's autowiring this interface will actually
get the `OutputtingXpCalculator` service. And if you look down here at the arguments,
the first argument passed to `OutputtingXpCalculator` is the *real* `XpCalculator`.
That's amazing!

## Multiple Decoration

All right, the decorator pattern is *done*. What a cool pattern! One feature of the
decorator pattern that we only mentioned is that you can decorate a service as many
times as you want. Yep! If we created *another* class that implemented
`XpCalculatorInterface` and gave it this `#AsDecorator()` attribute, there would now
be *two* services decorating it. Which service would be on the outside? If you care
enough, you could set a `priority` option on one of the attributes to control that.

## Decoration in the Wild?

Where do we see decoration in the wild? The answer to that is... sort of all over!
In API Platform, it's common to use decoration to extend core services like
the `ContextBuilder`. And Symfony *itself* uses decoration pretty commonly to add
debugging features while we're in the `dev` environment. For example, we know that
this `EventDispatcher` class would be used in the `prod` environment. But in the
*dev* environment - I'll hit `t` to search for a "TraceableEventDispatcher" - assuming
that you have some debugging tools installed, *this* is the actual class that
represents the `event_dispatcher` service. It *decorates* the real one!

I can prove it. Head back to your terminal and run:

```terminal
php ./bin/console debug:container event_dispatcher --show-arguments
```

Scroll to the top and... check it out! The `event_dispatcher` service is an *alias*
to `debug.event_dispatcher`... whose class is `TraceableEventDispatcher`! And if
you scroll down to its arguments, ha! *It's* passed our `DebugEventDispatcherDecorator`
as an argument. Yup, there are *3* event dispatchers in this case: Symfony's
core `TraceableEventDispatcher` is on the outside, it calls into *our*
`DebugEventDispatcherDecorator`... and then *that* ultimately calls the *real*
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
