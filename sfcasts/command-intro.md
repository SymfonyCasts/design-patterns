# Command Pattern

Ready for a new design patterns episode? Grab a cup of coffee and settle in,
because we're taking a deep dive into the *command pattern*! We'll start with
the basics - the definition and theory. *Later*, we'll have some fun by applying
what we've learned to our game application.

Let's begin with the most obvious question: What *is* the command pattern?
The command pattern is a *behavioral pattern*. If you forgot what that means, here's
a refresher. Behavioral patterns help us design classes with specific
responsibilities that can work together, instead of putting all of that code into one giant class.

The *official* definition of command pattern says that they "*encapsulate* a
request as a stand-alone object, allowing parameterization of clients with
different requests, queueing or logging requests, and support undoable operations."

Um... *what*?. Okay, let's try that again with a less confusing definition.
"The *command pattern* encapsulates a task into an object, decoupling what it does,
how it does it, and when it gets done. It also makes *undoing* actions
easy because it can keep a history of changes."

Still confusing? Don't worry! This will make *a lot* more sense when we see it in action.

## Pattern Anatomy

The command pattern is composed of three main parts:

First is the "Command Interface", which has a single public method that's
typically called `execute()`.

*Second* is the *concrete* commands, which *implement* the Command Interface and
hold the task's logic.

*Finally*, it has an *invoker* object which holds a command reference and, at
some point, calls `execute()`.

If you've read about this online, you may have noticed that I didn't mention two
other parts - the *receiver* and the *client*. The *receiver* is the object that
contains the business logic, and the *client* is in charge of creating command objects.

In my opinion, those elements increase the complexity of the design and aren't
*super* necessary. They may be useful in heavy, object-oriented applications so
responsibilities are better organized, but in our case, using these would
over-engineer our application. So, for simplicity's sake, we're just going to
ignore them.

## Imaginary Example

Okay, now that we've covered the *theory* side of things, let's see an example.
Suppose that we want to implement a remote control for a TV. Our remote has several
buttons that allow us to interact with our TV, like turning the volume up or down,
powering it on or off, and so on.

An easy way to do this is with a `switch-case` statement, where each `case`represents
a button's action with all of the logic it needs to perform that action.

```php
public function pressButton(string $button)
{
    switch ($button) {
        case 'turnOn':
            $this->powerOnDevice($this->tv->getAddress())
            $this->tv->initializeSettings();
            $this->tv->turnOnScreen();
            $this->tv->openDefaultChannel();
            break;
        case 'turnOff':
            $this->tv->closeApps();
            $this->tv->turnOffScreen();
            $this->powerOffDevice($this->tv->getAddress())
            break;
        case 'mute':
            $this->tv->toggleMute();
            break;
        ...
    }
}
```

That's pretty simple, but as we add more and more buttons, this is going
to get *complicated*. It's messy, hard to maintain, and we won't really be able to
reuse this code anywhere else.

There *has* to be a better way to do this... and there *is* - with the *command pattern*.
We can group the logic of each button into its *own* command object. Then,
in our `pressButton()` method, we would just call `execute()` on the command we want to perform.

It looks something like this:

```php
/**
 * @param array<string, ButtonCommandInterface> $commands
 */
public function __construct(private array $commands)
{
}

public function pressButton(string $button)
{
    if (!isset($this->commands[$button])) {
        throw new NotSupportedButtonException($button);
    }
    
    $this->commands[$button]->execute($this->tv);
}
```

The `commands` property is an array of button command objects, keyed by a string representation
of the button. When calling `pressButton()`, we look for the passed button name in this array
and call `execute()`. Pretty handy!

Instantiating this *TV remote* object (and the button commands), then *using* it, would look
something like this:

```php
$remote = new Remote([
    'turnOn' => new TurnOnCommand(),
    'turnOff' => new TurnOffCommand(),
    'mute' => new MuteCommand(),
]);

$remote->pressButton('mute'); // executes MuteCommand logic
```

The command pattern is *great*, and we can already see how helpful it can be.
We can add or remove buttons without touching the code in our `pressButton()` method,
and all of the logic is encapsulated into separate classes, making our
code easier to maintain and reuse. *And*, as a bonus, we've successfully applied
the *Open-Close* principle. This method is now *open* for extension, but *closed* for modification.

Next: Let's see the command pattern in action and implement it in our application!
