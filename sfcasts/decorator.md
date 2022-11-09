# Decorator Pattern

One more design pattern to go! And honestly, I think we may have saved the best for last. It's the *decorator* pattern. This pattern is a *structural* pattern, so it's all about how you organize and connect related classes. That will make more sense as we uncover it. Here's the technical definition:

The decorator pattern allows you to attach new behaviors to objects by placing these objects inside special *wrapper* objects that contain the behaviors.

Yeah... Let's try this definition instead:

The decorator pattern is like an intentional man in the middle attack. You replace a class with your custom implementation, runs some code, then call the true method.

Before we get any deeper and nerdier, let's see it in action.

Here's the goal: I want to print something onto the screen whenever a player levels up. The logic for leveling up is actually inside of `XpCalculator.php`, but instead of changing the code in *this* class, we're going to apply the decorator pattern so we can *add* stuff to this class without adding *code* to this class. This is a common pattern to use if `XpCalculator` were a vendor service that we were unable to modify, and *especially* if the that service didn't give us any other way to hook into it, such as not implementing the observer or strategy patterns.

For the decorator pattern to work, there's just one rule: The class that you want to decorate (meaning the class you want to extend or modify - `XpCalculator` in our case) needs to implement an interface. You'll see why in a few minutes. If `XpCalculator` were a *vendor* package, we just have to hope they did a good job and made it implement an interface. But since this is *our* code, we can add one. In the `/Service` directory, I'm going to create a new class and change it to an interface. We'll call it `XpCalculatorInterface`. Then, I'll go steal the method signature for `addXp()`, paste that here, add a `use` statement and a semicolon. Easy enough.

Over in `XpCalculator`, we'll implement `XpCalculatorInterface()`. Finally, open up `XpEarnedObserver.php`. This is the one place in our code we're using the `XpCalculator`. Change this to use the `XpCalculatorInterface` type hint.

This stuff *was* important. Why? Because we are now type hinting the *interface*, we're going to be able to swap out the *true* `XpCalculator` for our own implementation known as the *decorator*. So let's create that decorator class!

In the `/src/Service` directory, creating a new PHP class and let's call it, how about... `OutputtingXpCalculator`, since it's an `XpCalculator` that *outputs* things to the screen. The key thing with a decorator class is that this class is going to be responsible for calling the *real* method on the *real* service. This means we need to pass the real service into it. In other words, we're going to create a `public function __construct()` and this is going to accept a `private readonly XpCalculatorInterface` and we'll call it `$innerCalculator`. Our `OutputtingXpCalculator` *also* needs to implement the `XpCalculatorInterface` so that we can pass it to things like our observer. If I go to Code Generate and select "Implement Methods", this requires us to have the `addXp()` method. I'll add the missing `use` statement and... perfect!

The main thing the decorator is always going to do is call that inner service, so let's say `$this->addXp($winner, $enemyLevel)`. This basically says if something calls `addXp()` on the `OutputtingXpCalculator`, we will *then* call it on the `innerCalculator`. It would actually make more sense if I said `$this->innerCalculator->addXp()`, so let me change that. Nice!

You can see that we're creating a sort of chain here. That's one of the benefits of decorators , because we're going to have just two `XpCalculators` in our application - the outputting version and the *real* version. But technically, we could have *multiple* decorators. We could have decorators decorating other decorators, that decorate *other* decorators, and we can do this as many times as we want.

Down here, you can see that we really have the ability to run code before *and* after we call the inner service. So *before*, we're going to say `$beforeLevel = $winner->getLevel()`, which will store the level before we add the XP. Then, down here, we'll say `$afterLevel = $winner->getLevel()`. Finally, `if ($afterLevel > $beforeLevel)`, we'll know that we just leveled up.

This calls for a celebration, so let's print some stuff! I'll say `$output = new ConsoleOutput()`, which is just a cheap way to write console, and then I'll paste in a couple of lines here that will output a nice little `Congratulations! You\'ve leveled up!` message and what level you're on right now. Awesome! Our decorator class is now done! But how do we hook this up? What we want to do is replace any instances of `XpCalculator` in our system with our *new* `OutputtingXpCalculator`.

Let's do this manually first, without Symfony's fancy container stuff. There's only one place in our code that uses `XpCalculator` and it's our `XpEarnedObserver`, so I'm going to go into `/src/Kernel.php` and temporarily comment out this `subscribe` magic that we had from earlier. I'm doing this because, for the moment, I want to *manually* instantiate my `XpEarnedObserver` and *manually* subscribe that to our `GameApplication`.

We're going to do this inside of `/src/Command/GameCommand.php`. This is actually what we had earlier before we added that kernel magic. We can say `$xpCalculator = new XpCalculator()` and then `$this->game->subscribe(new XpEarnedObserver()` and pass it `$xpCalculator`. So now we're just manually calling `subscribe` on `GameApplication`.

You may have noticed that we're not using our new decorator yet, but it should still give us XP when we level up. If we head over and run our command... and *win*... Perfect! I won and my character's XP is increasing, which means that our `XpEarnedObserver` is doing its job. So how do we use our decorator? The answer is by sneakily replacing the *real* `XpCalculator` with the fake one. Say `$xpCalculator = new OutputtingXpCalculator()`, and then pass it the original `$xpCalculator`. That's *it*. Suddenly, even though it has no idea, the `XpEarnedObserver` is being passed our decorator service. How cool is that?

So let's start over. Run the game again and fight a few times. The new decorator we added *should* print a special message the moment that we level up. I'll fight one more time and... got it! We're now level *two*. It's working! If you're wondering why we leveled up before the battle actually started, that might be because these icons here are really for decoration. The battle has already finished by the time that happens

Okay, we have *successfully* created a decorator service. Awesome! So how do we hook this up and replace the core `XpCalculator with the decorator inside of Symfony's container? Let's find out *next*. Plus, we'll do something even *cooler*. We're going to completely replace a core Symfony service with our *own* custom decorator.
