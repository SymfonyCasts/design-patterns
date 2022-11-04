# Observer Inside Symfony + Benefits

We've implemented the Observer Pattern! The `GameApplication` is our subject, which notifies all of our observers, and we have *one* observer at the moment: `XpEarnedObserver()`. Inside of `GameCommand.php`, we connected them by manually instantiating them in the observer and `XpCalculator()` service, and then calling `$this->game->subscribe()`. But that isn't very Symfony-like.

Both the observer and `XpCalculator()` are *services*, so we would *normally* autowire them from the container. We *are* autowiring `GameApplication`, but that isn't quite right. By the time Symfony gives us this `GameApplication`, Symfony's container *should* have hooked up all of its observers already so it's ready to go *immediately*. How can we fix that? We'll do it the simple way.

First, let's remove all of the manual code that we don't want inside of `GameCommand.php`. We're going to configure the same thing you see here, but inside of `Services.yaml`. I'll open that up... and at the bottom, we're going to modify the service `App\GameApplication`, and instead of passing arguments to it, we're going to add `calls`. This basically says:

`After you instantiate GameApplication, call the
subscribe method on it and pass, as an argument,
@App\Observer\XpEarnedObserver.`

So when we autowire `GameApplication`, Symfony will go grab the `XpEarnedObserver` service and *that* service will get the `XpCalculator()` service autowired into it. This is pretty normal autowiring - the only difference being that Symfony is going to call the `subscribe` method on `GameApplication` before it gives us the `GameApplication` service inside of `GameCommand.php`. In other words, this *should* work. Let's give it a try! Run:

```terminal
./bin/console app:game:play
```

There are no errors so far and... oh. We lost. Bad luck. Let's try again! We won *and* we received 30 XP. It's working! *But* this means that every time we add a new observer, we'll need to go to `Services.yaml` and wire it *manually*. *gasp* How *undignified*...

Could we *automatically* subscribe all services that implement `GameObserverInterface`? Why, what an *excellent* idea! We can do that in two steps.

First, open `src/Kernal.php`. This isn't a file that we work with much, but we're about to do some deeper things with the container and this is exactly where we wanna be. I'll go to Code Generate or "command" + "O" and go to "Override Methods". We're going to override one called "build". Perfect! This `parent` method is empty, so we don't need to call it at all. Instead, rigtht here, we're going to say `$container->registerForAutoconfiguration()`, pass it `GameObserverInterface::class`, and then say `->addTag()`. I'm going to invent a new tag here called `game.observer`. This is probably not something you see very often (or ever), but this is something that Symfony Core and third party bundles do all the time. This says that any service that implements `GameObserverInterface` should automatically be given to this `game.observer` tag if that service is auto-configurable.

I just made up this tag name here, and it doesn't do anything at the moment. Giving a tag to a service is like tagging a blog post. But we *should* be able to see it, at least. If we spin over and run

```terminal
./bin/console debug:container xpearnedobserver
```

Beautiful! It found our service! Check it out: `Tags` `game.observer`.

All right, now that our service has a tag, we're going to write a little more code that automatically calls the `subscribe` method on `GameApplication` for *every* service that has that tag. This is *also* going to go in `Kernel.php`, but we're going to override a *different* method. In this case, we're going to implement something called a "compiler pass", so add a new interface to your `Kernel` called `CompilerPassInterface`. Then, down here, I'll go to Code Generate, "Implement Methods", and select "process".

A compiler pass is a little more advanced. It's a piece of code that runs at the *very* end of the container being built, and you can do whatever you want to inside of it. Check this out! Say `$definition = $container->findDefinition(GameApplication::class)`. This is not going to return us to the `GameApplication` service, but a `GameApplication` service *definition* - an object that knows everything about *how* to instantiate a `GameApplication`, like its class, constructor arguments, and any calls it might have on it.

Next, I'll say `$taggedObservers = $container->findTaggedServiceIds('game.observer')`. This is going to return an array of all the services that have this `game.observer` tag. Then we can look over them with `foreach ($taggedObservers as $id => $tags)`. The service ID is going to have that tag, and since it's possible for a service to be tagged multiple times, there will actually be *multiple* tags inside of this. But we don't need to worry about that. All we're going to do is say `$definition` (which is the `GameApplication` definition), and `->addMethodCall()` (which is the PHP version of the calls). Now we're going to have Symfony call the `subscribe` method on the game `$definition`, and for the arguments, pass it `new Reference()` (the one from dependency injection), and then `id`. This is a really fancy way of saying that we want the game of the `subscribe` method to be called on `GameApplication`, and for it to pass us the service that had the `game.observer` tag.

This is basically the same thing we have in `Services.yaml`, just more dynamic, so we can remove all of this code. If we try our game again...

```terminal
./bin/console app:game:play
```

No errors! And... yes! It *still* works! If we need to add another observer later on, we can just create a class, make it implement `GameObserverInterface` and... done! It will *automatically* be subscribed to the `GameApplication`.

So *that* is the observer pattern. It can look a bit different than this with different methods, and sometimes these observers are even injected through the constructor. But the idea is *always* the same: A central object loops over and calls a method on a collection of other objects when something happens.

Where do we see this in the wild? It shows up in a lot of places, but here's *one* example. Over on Symfony's GitHub page, I'm going to hit "T" and search for a class called `LocaleSwitcher`. If you need to do something in your application each time the locale switches, you can register your code with the `LocaleSwitcher` and it will call. In this case, the observers are actually passed through the constructor. And then you can see down here, after the locale is set, it loops over all of those and it sets the `setLocale()` method on it. So these classes are our subjects and these are our observers.

How do you register an observer? Not surprisingly, it's by creating a class that implements `LocaleAwareInterface`. Thanks to autowiring, Symfony will automatically tag your service with `kernel.localeAware`. So it uses the same mechanism for hooking all of this up, as we just saw.

The *benefits* of the observer pattern are actually best described by looking at the SOLID principles. This pattern helps the Single Responsibility pattern because you can encapsulate (or isolate) code in smaller classes. And instead of putting everything into `GameApplication`, like all of our XP logic right here, we were able to isolate things over into `XpEarnedObserver` and keep both classes more focused. This pattern also helps with the Open-closed Principle, because we can now extend the behavior of `GameApplication` *without* modifying its code by registering an observer.

This principle is always a bit tricky for me. The observer pattern follows the Dependency Inversion Principle, where the high-level class - `GameApplication` - accepts an interface - `GameObserverInterface` - and that interface is designed for the purpose of how game app will use it, like by calling the method `onFightFinished()`,for example. It's not designed based on how the observers will *use* it. If we had called the `XpChangerInterface` and the method `TimeToChangeTheXp`, *that* would be a violation of the Dependency Inversion Principle. If that's all a little fuzzy to you, it's no big deal, but you can watch our SOLID tutorial if you want to know more.

Next, let's quickly turn to the brother pattern of observer: *Pub/sub*.
