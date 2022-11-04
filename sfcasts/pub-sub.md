# Publish-Subscriber (PubSub)

The next pattern pattern I want to talk about maybe *isn't* its own pattern? In
reality, it's more of a *variation* of the observer pattern. It's called "pub/sub"
or "publish subscribe"

## PubSub vs Observer

The key difference be between observer and pub/sub is simply *who* handles notifying
the observers. With the observer pattern, it's the *subject* - the thing (in our
case, `GameApplication`) that does the work. With pub/sub, there's a third object -
usually called a "publisher" - than handles this. Except, instead of calling it
"publishing", I'm going to use a word that's probably more familiar to you: event
dispatcher.

With pub/sub, the observers (also called "listeners") tell the *dispatcher* which
events it wants to listen to. Then, the subject (whatever is doing the work) tells
the *dispatcher* to dispatch the event. The dispatcher is then responsible for
actually *calling* the methods on those objects.

You *could* argue that pub/sub better follows the Single Responsibility pattern.
Battling characters and also registering and calling the observers are two separate
responsibilities that we've jammed into `GameApplication`.

## Creating the Event

So here's the new goal: add the ability to run code *before* a battle starts by using
pub/sub.

Step one is to create an event class. This will be the object that is passed as an
argument to all of the listener methods. It's purpose is pretty much *identical*
to the `FightResult` that we're passing to our observers: it holds whatever data
might be interesting to the listeners.

With the pub/sub pattern, it's customary to create an event class *just* for the
event system. So inside of `src/`, I'm going to create a new `Event/` directory.
Then a new PHP class. You can call it whatever you want, but for this tutorial, let's
call it `FightStartingEvent`.

This class doesn't need to look like or extend anything, and we'll talk more about
it in a minute.

## Dispatching the Event

Step *two* is to dispatch this event *inside* of `GameApplication`. Instead of
writing our own event dispatcher, we're going to use Symfony's EventDispatcher. Let
me break the constructor onto multiple lines... and let's add a new
`private EventDispatcherInterface $eventDispatcher`.Then, down in `play()`, right
at the very beginning, say `$this->eventDispatcher->dispatch()` passing
a `new FightStartingEvent()`.

That's *it*! That's enough for the dispatcher to notify all of the code that is
listening to the `FightStartingEvent`. Of course... at the moment, *nothing* is
listening!

## Registering Listeners... Manually

So *finally*, let's register a listener to this event. Open `GameCommand`: the place
where we're and initializing our app. We'll see how to do all of this properly with
Symfony's container in a minute, but I want to keep it simple to start. In the
constructor, add `private readonly EventDispatcherInterface $eventDispatcher`.

I know, I *am* being a little inconsistent between when I use `readonly` and not.
Technically, I *could* use `readonly` on *all* of the construct arguments... it's
just not something I care about too much.

## Choosing the Correct EventDispatcherInterface

Down here, anywhere before our app actually starts, say `$this->eventDispatcher->`.
Notice that the only method on this is `dispatch()`. I made a... *tiny* mistake.
Let's back up for a minute. In `GameApplication`, when I autowired
`EventDispatcherInterface`, I chose the one from
`Psr\EventDispatcher\EventDispatcherInterface`, which contains the `dispatch()`
method we need. So that's *great*.

Inside of `GameCommand`, we autowired that *same* interface. But if you want the
ability to attach listeners at *run time*, you need to autowire
`EventDispatcherInterface` from `Symfony\Component\EventDispatcher` instead of `Psr`.

In reality, regardless of which interface you use, Symfony will *always* pass
us the *same* object. That object *does* have a method on it called `addListener()`.
So even if I had used the *previous* interface, this method *would* have existed...
it just would have looked funny inside of my editor.

Anyways, the first argument of this is the *name* of the event, which is going to
match the class name that we're dispatching. So we can say
`FightStartingEvent::class`. And then, to keep it simple, I'm going to be lazy and
pass an inline `function()`. I'll also `use ($io)`... so that inside I can say
`$io->note('Fight is starting...')`

And... done! We're dispatching the event inside of `GameApplication`... and since
we've registered the listener here, it should be called!

Let's try it! At your terminal, say:

```terminal
./bin/console app:game:play
```

We'll choose our character and... got it - `[NOTE] Fight is starting...`. If we fight
again... we get the *same* message. *Awesome*!

Next, let's make this more powerful by passing information to our listener, like
*who* is about to battle. Plus, we'll see how the event listener system is used
in a *real* Symfony app using the container to wire everything up.