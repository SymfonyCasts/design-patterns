# Pub Sub Event Class & Subscribers in Symfony

We *are* able to run code right *before* a battle finishes by registering what's
called a "listener" on to `FightStartingEvent`. As you can see, a listener can
be any function... though what we see here is a bit less common. Usually a listener
is a method inside a class. And we'll refactor to that in a few minutes.

## Passing Data to Listeners

But before we do, it might be useful to have a little bit more info in our listener
function, like *who* is about to battle. That's the job of this event class. It can
carry *whatever* data we want. For example,create a
`public function __construct()` with two properties... which I'm going to make
public for simplicity: `$player` and `$ai`.

Cool! Over in `GameApplication`, we need to pass those in: so pass `$player` and
`$ai`.

Back over in our listener, this function will be passed a `FightStartingEvent` object.
In fact, it was *always* being passed... it just wasn't useful before. Now we can
say `Fight is starting against`, followed by `$event->ai->getNickname()`.

*Super nice*. Give it a try! I'll run the command again and... sweet! We see

> [NOTE] Fight is starting againstAI: Mage.

The only thing I did is miss my space after "against" so it looks nicer. I'll fix
that really quick.

## Allowing Listeners to Control Behavior

As I mentioned, you can really put *whatever* data you want inside
`FightStartingEvent`. Heck, you could create a `public $shouldBattle = true` property
if you wanted. Then, in a listener, you could say `$event->shouldBattle = false`...
maybe because the characters have used *communication* and *honesty* to solve
the problems. Bold move.

Anyways, in `GameApplication`, you could then set this event to a new `$event` object,
dispatch it, and if they *shouldn't* battle, just `return`. Or you could
`return new FightResult()` or throw an exception. Either way, you see the point.
Your listeners can, in a sense, communicate *back* to the central object to control
some behavior.

I'll undo all of that inside of `GameApplication`, `FightStartingEvent` and also
`GameCommand`.

## Creating an Event Subscriber

As easy as this inline listener is, it's more common to create a separate class for
your listener. You can either create a *listener* class, which is basically a class
that has this code here as a public function, *or* you can create a class called
a *subscriber*. Both are completely valid ways to use the pub/sub pattern. The only
difference is how you *register* a listener versus a subscriber, which is pretty
minor, and you'll see that in a minute. Let's refactor to a subscriber because
they're easier to setup in Symfony.

In the `Event/` directory, create a new PHP class called... how about...
`OutputFightStartingSubscriber`, since this subscriber is going to *output* that
a battle is beginning. Events listeners don't need to extend any base class or
implement any interface, but event *subscribers* do. They need to implement
`EventSubscriberInterface`. Then I'll go "Code Generate" or "Command" + "N" on a
Mac, go to ""Implement Methods"", and select `getSubscribedEvents()`.

Nice! With an event subscriber, you'll list which events you subscribe to right
inside this class. So we'll say `FightStartingEvent::class => 'onFightStart'`. This
says:

> When the `FightStartingEvent` happens, I want you to call the `onFightStart` method
> right inside this class!

Create that: `public function onFightStart()`... and it will receive a
`FightStartingEvent` argument. For the guts of this, go over to `GameCommand`
and steal the `$io` line. By the way, the `$io` object is kind of hard to pass
from console commands into other parts of your code... so I'm going to ignore that
complexity here and just create a new one with
`$io = new SymfonyStyle(new ArrayInput([]), new ConsoleOutput()`.

Now that we have a subscriber, back in `GameCommand`, let's hook that up. Instead
of `addListener()`, say `addSubscriber()`, and inside of that,
`new OutputFightStartingSubscriber()`.

Easy! Testing time! I'll exit, choose my character and... wow! It's working *so*
well, it's outputting *twice*. We're amazing!

But... seriously, why is it printing twice? This is, once again, thanks to
`autoconfiguration`! Yup, whenever you create a class that implements
`EventSubscriberInterface`, Symfony's container is *already* taking that
and registering it on the `eventDispatcher`. In other words, Symfony,
internally, is already calling this line right here. So I'll delete this.

I guess that answers the question of "how we use the pub/sub pattern in Symfony?.
Just create a class, make it implement `EventSubscriberInterface` and... done!
Symfony will automatically register it. To *dispatch* an event, create a new event
class and dispatch that event anywhere in your code.

If we try this again (I'll exit the battle first)... it only outputs *once*. Great!

And... what are the benefits of pub/sub? They're really the same as the observer,
though, in practice, pub/sub is a bit more common... probably because Symfony already
has this great event dispatcher. Half of the work is already done *for* you!

Next, let's dive into our final pattern! It's one of my favorites *and* the most
powerful in Symfony: The *decorator* pattern.
