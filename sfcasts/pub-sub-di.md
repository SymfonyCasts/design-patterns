# Pub Sub Event Class & Subscribers in Symfony

We *are* able to run code right *before* a battle starts by registering what's
called a "listener" to `FightStartingEvent`. As you can see, a listener can
be any function... though what we see here is a bit less common. Usually a listener
will be a method inside a class. And we'll refactor to that in a few minutes.

## Passing Data to Listeners

But before we do, it might be useful to have a little bit more info in our listener
function, like *who* is about to battle. That's the job of this event class. It can
carry *whatever* data we want. For example, create a
`public function __construct()` with two properties... which I'm going to make
public for simplicity: `$player` and `$ai`:

[[[ code('903f93271c') ]]]

Cool! Over in `GameApplication`, we need to pass those in: `$player` and `$ai`:

[[[ code('c25b2d160e') ]]]

Back over in our listener, this function will be passed a `FightStartingEvent` object.
In fact, it was *always* being passed... it just wasn't useful before. Now we can
say `Fight is starting against`, followed by `$event->ai->getNickname()`:

[[[ code('ad2eaad82b') ]]]

*Super nice*. Give it a try! I'll run the command again and... sweet! We see

> ! [NOTE] Fight is starting against AI: Mage

The only thing I missed is the space after "against" so it looks nicer. I'll fix
that really quick:

[[[ code('680f77c43f') ]]]

## Allowing Listeners to Control Behavior

As I mentioned, you can really put *whatever* data you want inside
`FightStartingEvent`. Heck, you could create a `public $shouldBattle = true` property
if you wanted. Then, in a listener, you could say `$event->shouldBattle = false`...
maybe because the characters have used *communication* and *honesty* to solve
their problems. Brave move!

Anyways, in `GameApplication`, you could then set this event to a new `$event` object,
dispatch it, and if they *shouldn't* battle, just `return`. Or you could
`return new FightResult()` or throw an exception. Either way, you see the point.
Your listeners can, in a sense, communicate *back* to the central object to control
its behavior.

I'll undo all of that inside of `GameApplication`, `FightStartingEvent` and also
`GameCommand`.

## Creating an Event Subscriber

As easy as this inline listener is, it's more common to create a separate class for
your listener. You can either create a *listener* class, which is basically a class
that has this code here as a public function, *or* you can create a class called
a *subscriber*. Both are completely valid ways to use the pub/sub pattern. The only
difference is how you *register* a listener versus a subscriber, which is pretty
minor, and you'll see that in a minute. Let's refactor to a subscriber because
they're easier to set up in Symfony.

In the `Event/` directory, create a new PHP class called... how about...
`OutputFightStartingSubscriber`, since this subscriber is going to *output* that
a battle is beginning:

[[[ code('aa599a4224') ]]]

Event listeners don't need to extend any base class or implement any interface,
but event *subscribers* do. They need to implement `EventSubscriberInterface`:

[[[ code('118c784a67') ]]]

Go to "Code" -> "Generate" or `Command`+`N` on a Mac and select "Implement methods"
to generate `getSubscribedEvents()`:

[[[ code('d69a200d67') ]]]

Nice! With an event subscriber, you'll list which events you subscribe to right
inside this class. So we'll say `FightStartingEvent::class => 'onFightStart'`:

[[[ code('5bdee2bbdf') ]]]

This says:

> When the `FightStartingEvent` happens, I want you to call the `onFightStart()` method
> right inside this class!

Create that: `public function onFightStart()`... which will receive a
`FightStartingEvent` argument:

[[[ code('ad40bd69ae') ]]]

For the guts of this, go over to `GameCommand` and steal the `$io` line:

[[[ code('4b72734719') ]]]

By the way, the `$io` object is kind of hard to pass from console commands into
other parts of your code... so I'm going to ignore that complexity here and just
create a new one with `$io = new SymfonyStyle(new ArrayInput([]), new ConsoleOutput()`:

[[[ code('b7440fd623') ]]]

Now that we have a subscriber, back in `GameCommand`, let's hook that up! Instead
of `addListener()`, say `addSubscriber()`, and inside of that,
`new OutputFightStartingSubscriber()`:

[[[ code('0c014a36c1') ]]]

Easy! Testing time! I'll exit, choose my character and... wow! It's working *so*
well, it's outputting *twice*. We're amazing!

But... seriously, why is it printing twice? This is, once again, thanks to
auto-configuration! Whenever you create a class that implements
`EventSubscriberInterface`, Symfony's container is *already* taking that
and registering it on the `EventDispatcher`. In other words, Symfony,
internally, is already calling this line right here. So, we can delete it!

[[[ code('70a13c5624') ]]]

I guess that answers the question of:

> How do we use the pub/sub pattern in Symfony?

Just create a class, make it implement `EventSubscriberInterface` and... done!
Symfony will automatically register it. To *dispatch* an event, create a new event
class and dispatch that event anywhere in your code.

If we try this again (I'll exit the battle first)... it only outputs *once*. Great!

And... what are the benefits of pub/sub? They're really the same as the observer,
though, in practice, pub/sub is a bit more common... probably because Symfony already
has this great event dispatcher. Half of the work is already done *for* us!

Next, let's dive into our final pattern! It's one of my favorites *and*, I think,
the most powerful in Symfony: The *decorator* pattern.
