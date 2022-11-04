# Pub Sub Event Class & Susbcribers in Symfony

Coming soon...

But it would be *cooler* if we had a
little more information inside of our listener, like who *won* the battle. That's the
job of this event class. It can carry whatever data we want. For example, let's
create a `public function __construct()`. For simplicity, I'm going to create two
`public` properties called `$player` and `$ai`. *Awesome*! Over in `GameApplication`,
we need to actually pass those in, so pass `$player` and `$ai` here. Then, over in
our listener, our function will be passed a `FightStartingEvent` object. It always
*was* - it just wasn't very useful before. Now we can say `Fight is starting
against`, followed by `$event->ai->getNickname()`. *Super nice*. Let's give it a try!
I'll run the command again and... sweet! We see `[NOTE] Fight is starting againstAI:
Mage`. The only thing I did is miss my space after "against" so it looks nicer. I'll
fix that really quick.

As I mentioned, you can really put whatever data you want on this
`FightStartingEvent`. Heck, you could create a `public $shouldBattle =  true`
property if you wanted to. And for your listener, you could say `$event->shouldBattle
= false`. In `GameApplication`, you could actually set this event to a new `$event`
object, dispatch it, and if they *shouldn't* battle, it would just `return`. Or you
could `return FightResults()` and maybe throw an exception. Either way, you see the
point. You can even send signals back about what you want to do. I'll undo all of
that inside of `GameApplication`, `FightStartingEvent` and also `GameCommand`.

As easy as this inline listener is, it's more common to create a separate class for
your listener. You can either create a *listener* class, which is basically a class
that has this code here as a public function, *or* you can create a class called a
*subscriber*. Both are completely valid ways to use the pub/sub pattern. The only
difference is how you *register* a listener versus a subscriber, which is pretty
minor, and you'll see that in a minute. So let's refactor this to a subscriber.

In the `/Event` directory, create a new PHP class called... how about...
`OutputFightStartingSubscriber`, since this subscriber's going to *output* that a
battle is beginning. Events listeners don't need to extend any base class or
implement any interface, but event *subscribers* do. They need to implement the
`EventSubscriberInterface`. Then I'll go "Code Generate" or "Command" + "N" on a Mac,
go to ""Implement Methods"", and select `getSubscribedEvents()`. Nice! With an event
subscriber, you'll list which events you subscribe to right inside this class. So
we'll say `FightStartingEvent::class => 'onFightStart'`. This basically says:

`When that event happens, I want you to call the
onFightStart method in this class`.

So we'll create that with `public function onFightStart(FightStartingEvent $event)`.
This will get the `FightStartingEvent` object. Then, for the guts of this, I'll go
over to `GameCommand` and steal our `$io`. It's important to note that the `$io` is
kind of hard to pass from console commands into other parts of your code. I'm going
to ignore that complexity here and just create a new `$io` by saying `$io = new
SymfonyStyle(new ArrayInput([]), new ConsoleOutput()`. I'm creating an object that's
just like the normal `$io` object so I can cheat a little here.

Now that we have a subscriber, back in `GameCommand`, we're going to hook that up. So
instead of `addListener()`, say `addSubscriber()`, and inside of that, `new
OutputFightStartingSubscriber()`. Awesome!

Testing time! Moment of *truth*. I'll exit, choose my character and... wow! It's
working *so* well, it's outputting *twice*. Why? This is, once again, thanks to auto
configuration. We're using auto configuration inside of our application, so whenever
we create a class that implements `EventSubscriberInterface`, the Symfony container
is automatically taking that subscriber and registering it on the `eventDispatcher`.
In other words, Symfony, internally, is already calling this line right here. So that
answers the question of how we use the pub/sub pattern in Symfony.

I'll go delete that line inside of `GameCommand`. All you need to do is create an
event subscriber like we've done here, and Symfony will automatically register it.
Then, to *dispatch* an event, you'll create a new event class and dispatch that event
anywhere in your code.

So you can see how easy it is to create the `eventDispatcher` and dispatch *all
kinds* of events all over your application. If we try this again (I'll exit I battle
first)... it only outputs *once*. Great! The benefits of pub/sub are really the same
as the observer, but in practice, pub/sub is a bit more common. That's probably
because Symfony has this great event dispatcher. Half of the work is already done
*for* you.

Next, let's dive into our final pattern! It's one of my favorites *and* the most
powerful in Symfony: The *decorator* pattern.
