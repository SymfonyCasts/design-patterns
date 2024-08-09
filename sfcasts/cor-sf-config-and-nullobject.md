# Configuring CoR with Symfony

This is a Symfony app, so let's take advantage of that and use the *autoconfigure* feature
to set up our chain. There's even a super useful `Autoconfigure` attribute
we can use in our handler classes.

We'll start by opening `CasinoHandler` and, above the class name, add
the `#[Autoconfigure()]` attribute. When this class is *instantiated*, we want
to call `setNext()` and pass another handler object. To do that, we'll use
the `calls` option, so inside
write `calls: [['setNext' => ['@'.LevelHandler::class]]]`. Be careful with this nested array syntax.

Now, when Symfony instantiates this class, it will call `setNext()` and pass a `LevelHandler` object.

By the way, if you're wondering what this `@` symbol prefix is all about, good eye!
This tells Symfony to pass the `LevelHandler` _service_. If we didn't add this,
it would pass the `LevelHandler` class string which is definitely not what we want.

We'll do the same thing in the `LevelHandler` class. Open that up,
write `#[Autoconfigure()]`, and
then `calls: [['setNext' => ['@'.OnFireHandler::class]]]`. We can leave
the `OnFireHandler` as it is because it's the last handler in the chain.
*Perfect*!

Now that the chain is set up, we can remove the code that manually
initializes it in `GameApplication`. Open that... and delete everything inside its
constructor except this line. The *final* step is to configure
the `$xpBonusHandler` property. We can add it to the constructor by
writing `private readonly XpBonusHandlerInterface $xpBonusHandler`. Above
*that*, use the `#[Autowire]` attribute, and inside,
write `service: CasinoHandler::class` because it is the first handler
in our chain.

We need the `#[Autowire]` attribute because Symfony won't know how to inject
`XpBonusHandlerInterface` as there are multiple classes that implement
it.

All right! Let's give this a try! Spin over to your terminal and run:

```terminal
php bin/console app:game:play
```

Okay, the game *is* running. That's a good sign! And if we battle... we *lost*!
On the bright side, we can see our info message telling us that
the `CasinoHandler` kicked in, and we didn't get any extra XP, so everything
is working nicely.

## Bonus: Null Object Pattern

Ready for a bonus topic? It's time to talk about the *Null Object* pattern.
What *is* the Null Object pattern? In a nutshell, it's a smart way to avoid `null`
checks. Instead of checking to see if a property is `null`, as we've done in the
past, we'll create a "null object" that implements the same interface and does
nothing in their methods. What does this mean? Put simply, if a method returns a
value, it will return as close to null as possible. For example, if it returns
an array, you can return an empty array, or an empty string, or a 0 for `int`.
It can get even more complicated that this, but you get the idea.

So let's get to it! Back in our code, find that `if` we've been talking about,
which is inside any handler. What we need to do is remove the `if` and call the
next handler *directly*. *Easy peasy*. Create a new handler class and call
it `NullHandler`. Make it implement the `XpBonusHandlerInterface` and hold 
"Option" + "Enter" to implement the methods. And now... Let's have the handler
do *nothing*! Well, as close to nothing as possible.
The `setNext()` method returns nothing, so we can leave it empty, but
the `handle()` method returns an `int`. When you find a method that returns
something, you should always ask yourself how the value is being used. The
answer will help you identify the closest-to-null value to return. In *our*
case, we can return `0` and that will be fine because it represents the extra XP
the player will earn. If this value were used for *multiplication* however, like
a value that multiplies the XP earned for each match, returning `0` would cause
problems.

Okay, let's keep going! Open up `CasinoHandler` and add a constructor where
we'll initialize `$this->next` to a `new NullHandler()` object. Copy this
constructor because we'll need it for the other handlers. Inside the `handle()`
method, find that pesky `if` and remove it. We'll also remove this `return 0` at
the bottom. Now we'll always return the output of the next handler. That's the
*beauty* of the Null Object pattern. We'll do the same thing to the other
handlers, and... perfect! Let's give this a try!

Spin over to your terminal and run:

```terminal
php bin/console app:game:play
```

Hey hey! We won! *And* we earned extra XP thanks to the `LevelHandler`!
Everything's working and the code looks *great*, but I know a little trick that
could make this *even better*. We *could* make the `$next` handler property a
constructor argument and inject `NullHandler` into the last one in the chain
using the `Autowire` attribute we've seen before, like this:

```php
class OnFireHandler implements XpBonusHanderInterface
{
    public function __construct(
        #[Autowire(service: NullHandler::class)]
        private readonly XpBonusHanderInterface $next
    ) {
    }
}
```

This would even allow us to remove the `setNext()` method from the interface,
which is pretty handy. I don't want to deviate that much from the original
pattern's design, so I'll undo that, but it's good to keep in mind.

Next: Meet the Chain of Responsibility's *cousin* - the *Middleware* pattern.
