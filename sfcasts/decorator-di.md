# Decoration with Symfony's Container

We just implemented the decorator pattern, where we effectively wrapped the original `XpCalculator` with our `OutputtingXpCalculator`, then slipped that into the system in place of the original without anything else, like `XpEarnedObserver`, knowing or caring about it. But to set up the decoration part, I'm instantiating the objects *manually*, which is not very realistic. We really want `XpEarnedObserver` to autowire `XpCalculatorInterface` like normal, *without* us having to do any of this manual instantiation. But it needs to be passed our `OutputtingXpCalculator` decorator service. How can we accomplish that with the container? How can we tell the container that whenever someone type hints `XpCalculatorInterface`, we'll need it to pass us the decorator? First and foremost, let's undo our manual code.

In both `GameCommand.php` and `Kernel.php`, I'll put back the code that attaches the observer to `GameApplication`. If we try it now... it *fails*. We see the message:

`Cannot autowire service
"App\Observer\XpEarnedObserver": argument
"$xpCalculator" of method "__construct"
references interface
"App\Service\XpCalculatorInterface" but no such
service exists. You should maybe alias this
interface to one of these existing services:
"App\Service\OutputtingXpCalculator",
"App\Service\XpCalculatorInterface".`

This makes sense. Inside of our observer, we're now type hinting the *interface* instead of a concrete class, so unless we do a little more work, Symfony doesn't know which `XpCalculatorInterface` service to pass us. The way we tell it which service to pass for an interface is by creating an *alias*. In `/config/services.yaml`, we'll say `App\Service\XpCalculatorInterface`, and to make this an alias to *another* service, we'll just set it directly to `@App\Service\OutputtingXpCalculator`. Awesome! Now, anywhere in the system that was autowiring `XpCalculatorInterface` will now receive our decorator.

All right, let's try it again. And... it *still* doesn't work:

`Circular reference detected for service
"App\Service\OutputtingXpCalculator", path:
"App\Service\outputtingXpCalculator ->
App\Service\OutputtingXpCharacter"`

That makes sense too. Symfony is autowiring our `OutputtingXpCalculator` into `xpEarnedObserver`, but it's *also* autowiring `OutputtingXpCalculator` into *itself*. Whoops! We want our `OutputtingXpCalculator` to be used *everywhere* in the system that autowires `XpCalculatorInterface` *except* for this one case.

To do that, back in `services.yaml`, we can just manually configure that service. Down here, say `App/Service/OutputtingXpCalculator`, and then add `arguments`, `$innerCalculator` (that's the name of our arguments out of this), and we'll set this to `@App/Service/XpCalculator`. This will override what's going to be passed in for just this *one* case. And now... it seems to work! If we play a few rounds and fast forward... yes! There's the "you've leveled up" message! It *did* go through our decorator. Now that this is working, I'll go close a few classes.

This way of wiring our decorator is *not* our final solution, but before we get there, I have an even *bigger* challenge. Imagine that there's some core Symfony service and you want to extend its behavior with your own. How can we do that? Well, we *could* subclass the core service and reconfigure things so that maybe Symfony's container uses *our* class for that. That *might* work, but this is the place where decoration shines. So, as a challenge, let's extend the behavior of Symfony's core `EventDispatcher` service so that whenever an event is dispatched, we dump a debugging message.

The ID of the service that we want to decorate is called `event_dispatcher`. As I mentioned, we're going to extend the behavior of that core service through decoration first. Fortunately, the `EventDispatcher` *does* implement an interface. Over on GitHub for Symfony, I'll look for `EventDispatcher.php`. This is the actual core service, and you can see it implements `EventDispatcherInterface`. Cool! So let's go create our decorator class.

I'm going to create a new `/Decorator` directory. Inside of that, create a new PHP class called... how about `DebugEventDispatcherDecorator`. Step one, as you've probably already guessed, is to implement `EventDispatcherInterface`. This is a little tricky because there's actually *three* of them. There's `Psr`, which is the smallest one, this one from `Contract`, and *this* one from `Component`. We actually want to use the base one here, which is the biggest, from `Symfony/Component`. The *reason* is that, if our `EventDispatcher` decorator is going to be passed around this system in place of the real one, it needs to implement the *strongest* interface - the interface that has the *most* methods on it.

I'm going to go to Code Generate or "command" + "N" on a Mac, and select "Implement Methods" to implement the bunch that's needed. Whew... There we go! The other thing we need to do is add a constructor where the inner `EventDispatcherInterface` is passed in, so let me make that property `private readonly`. Perfect! Now we just need to call all of these methods. This part is simple, but boring. Let's say `$this->eventDispatcher->addListener($eventName, $listener, $priority)`.

One other thing you need to worry about is whether or not the parent method returns a value. That's not always obvious. You don't need to return in this method, but there are methods down here that *do* have return values, like `getListeners()`. I'm not going to bore you by doing the same thing we did up here about eight different times, so I'm going to delete all of these and I'm going to paste in the finished version of all those methods. You can copy this from the code block on this page. We're just calling the all of the listeners with the same methods and arguments inside of all of them. At this point, *congratulations*! We've created a decorator for our `EventDispatcher` that doesn't do anything extra yet.

Now let's add our behavior. Right before the `dispatch()` method is called, we're going to dump some information about the event. I'm going to paste these two dumps here. Then, right here, I'll say `Dispatching event`. After that, we can say `$event::class`. Perfect!

So here's the million dollar question: There are *many* places in Symfony that rely on the service whose ID is `event_dispatcher`, so how can we replace that service with our *own* service but still get the original service passed to us? The answer: Symfony has a feature built *specifically* for this. You're going to love it! Go above this class and add a PHP 8 attribute called `#[AsDecorator()]` and then pass it the name of the service that we want to decorate: `event_dispatcher`. That's *it*. This says:

`Hey Symfony, please make me the real
event_dispatcher service, but make the original
event_dispatcher service available to be
autowired as an argument to me.`

Let's try it! Run our app and... it works! Look! You can see the event being dumped out right here, and *another* one which is our custom event. And if I exit... another event at the bottom! It works!

We just replaced the core `EventDispatcher` service with our own by creating a *single* class. That's bananas! So could we have used this `AsDecorator` trick earlier for our own `XpCalculator` decoration situation? Yep! Here's how: In `/config/services.yaml`, remove the manual arguments and change the interface so it actually points to the real service we want - `XpCalculator`. Basically, in our `Service` config, we get to set things up the normal way without thinking or worrying about decoration. This is how we would want our application to work if there were no decorators.

If we tried our app right now, it *would* work, but we're not using our `OutputtingXpCalculator` decorator service. But *now*, if we go into `OutputtingXpCalculator`, we can add `#[AsDecorator()]` and then pass `XpCalculatorInterface::class`, since that's the ID of the service we want to decorate. And that's it! If we try this now... *no errors*. An even faster way to prove this is working is by running:

```terminal
./bin/console debug:container XpCalculatorInterface --show-argument
```

This will give me some information about what the `XpCalculatorInterface` service is. When I run this... check this out! It says that this is an *alias* for the service `OutputtingXpCalculator`. So anyone that's autowiring this is actually getting the `OutputtingXpCalculator` service. And if you look down here at the arguments, the first argument passed to `OutputtingXpCalculator` is the *real* `XpCalculator`. That's awesome!

All right, the decorator pattern is *done*. What a cool pattern! One feature of the decorator pattern that we *didn't* talk about was that you can decorate a service as many times as you want. Yep! If we created *another* class that implemented `XpCalculatorInterface` and gave it this `AsDecorator` attribute, there would now be *two* services decorating it. Which service would be on the outside? If you care enough, you can set a `priority` on these services to control that.

So where do we see decoration in the wild? The answer to that is... sort of all over. In API Platform, it's very common to use decoration to extend core behaviors like the ContextBuilder or parts of the Serializer. Symfony *itself* uses decoration pretty commonly to add debug features while you're in developing mode only. For example, we know that this `EventDispatcher` is the real service that would be used in the prod environment. But in the *dev* environment - I'll hit "T" to search for a "TraceableEventDispatcher" - assuming that you have some debugging tools installed, *this* is the actual service that represents the `EventDispatcher`. It *decorates* the real one.

I can prove it. Head back to your terminal and run:

```terminal
./bin/console debug:container event_dispatcher --show-arguments
```

Scroll to the top and... check this out! The `event_dispatcher` service is actually an *alias* for another service called `DebugEventDispatcherDecorator`. Yep, you guessed it! This is the `TraceableEventDispatcher`! So it's actually on the *outside*. And if you scroll down to its arguments, the next one that's inside of it is our `DebugEventDispatcherDecorator`. So you have the outer `TraceableEventDispatcher`, our `DebugEventDispatcherDecorator` inside of it, and then the *real* `EventDispatcher` inside of *that*. It's like a fancy ogre... I mean *onion* of code. Pretty cool!

So what problems do the decorator pattern solve? This one is pretty obvious. It allows us to extend the behavior of an *existing* class, like `XpCalculator` for example, even if that class doesn't contain any other extension points. This means we can use it to override vendor services when all else fails. The only downside to the decorator pattern is that we can only run code *before* or *after* the core method. And the service we want to decorate *must* implement an interface.

Okay, team. We are *done*! There are *many* more patterns out there in the wild. This was just a collection of some of our favorites. If we skipped one or several that you really want to hear about, let us know! Until then, see if you can spot these patterns in the wild and figure out where you can apply them to clean up your own code and impress your friends.

Thanks for coding with me, and I'll see you next time!
