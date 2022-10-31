# Builder in Symfony & with a Factory

What if, in order to instantiate the `Character` objects, `CharacterBuilder` needed
to, for example, make a *query* to the database? Well, when we need to make a query
to the database, we normally give our class a constructor and then autowire the
entity manager service. But `CharacterBuilder` *isn't* a service. You could *technically*
use it like a service, but a service is a class that typically only needs a *single*
instance. In `GameApplication` however, we're creating one `CharacterBuilder`
*per* character. If we *did* try to autowire `CharacterBuilder` into
`GameApplication`, that *would* work. Symfony *would* autowire the EntityManager
into `CharacterBuilder` and then it *would* pass us the `CharacterBuilder` object
with `$entityManager` inside. The *problem* is we would only have one
`CharacterBuilder` when we actually need *four* to create our four `Character`
objects.

*This* is why builder objects are commonly partnered with a builder *factory*. Let
me undo all of the changes I just made to `GameApplication`... and `CharacterBuilder`

## Creating a Factory

Over in the `Builder/` directory, create a new class called `CharacterBuilderFactory`.
By the way, there *is* a pattern called the factory pattern, which we won't
*specifically* cover in this tutorial. But a "factory" is just a class whose job
is to create *another* class. It, like the builder pattern, is a *creational pattern*.
Inside of the factory class, create a new method called, how about...
`createBuilder()`, which will return a `CharacterBuilder`. And inside of *that*,
just `return new CharacterBuilder()`.

This `CharacterBuilderFactory` *is* a service. Even if we need *five*
`CharacterBuilder` objects in our app, we only need *one* `CharacterBuilderFactory`.
We'll just call this method on it *five* times.

That means, over in `GameApplication`, we can create a
`public function __construct()` and autowire
`CharacterBuilderFactory $characterBuilderFactory`. I'll also add `private` in front
of that so it's a property.

Then, down inside of our `createCharacterBuilder()`, instead of creating this by
hand, rely on the factory: `return $this->characterBuilderFactory->createBuilder()`.

The nice thing about this factory (and this is really the purpose of the factory
pattern in general) is that we have *centralized* the instantiation of this object.

## Getting Services into the Builder

How does that help our situation? Remember, the problem I imagined was this:
What if our character builder needed a service like the EntityManager?

With our new setup, we can make that happen. I don't actually have Doctrine installed
in this project, so instead of the EntityManager, let's autowire
`LoggerInterface $logger`... and I'll again add `private` in front to turn that into
a property.

Then, down in `buildCharacter()`, just to test that this is working, use it:
`$this->logger->info('Creating a character')`. I'll also pass a second argument
with some extra info like `'maxHealth' => $this->maxHealth` and
`'baseDamage' => $this->baseDamage`.

`CharacterBuilder` now requires a `$logger`... and we can very easily handle that
in `CharacterBuilderFactory`. This *is* a service, so autowiring a `LoggerInterface`
will work here. Then, just pass that *manually* into the builder as `$this->logger`.

We're seeing some of the benefits of the factory pattern here. Since we've already
centralized the creation of the `CharacterBuilder`, anywhere that *uses* this factory,
like `GameApplication`, doesn't need to change at all. So even though we just
added a new constructor argument to `CharacterBuilder`, `GameApplication` doesn't
need to change. It was already offloading the instantiation work to `CharacterBuilderFactory`.

To see if this is working, run

```terminal
./bin/console app:game:play -vv
```

The `-vv` will let us see log messages while we play. And... got it! Look! Our
`[info] Creating a character` message popped up. We can't see the other stats we
passed on this screen, but they *are* in the log file. *Awesome*.

## What does The Builder Pattern Solve?

So *that's* the builder pattern. What problems can it solve? Simple! You have an
object that's difficult to instantiate, so you add a builder class to make life
easier. It also helps with the Single Responsibility Principle. It's one of the
strategies that helps abstract creation logic of a class *away* from the class that
will work with that object. Previously, in `GameApplication`, we had the
complexity of both *creating* the `Character` objects *and* using them. We still
have code here to use the builder, but most of the complexity now lives in the builder
class.

## Does my Builder Need an Interface?

Frequently, when you study this pattern, it will tell you that the builder
(`CharacterBuilder`, for example) should implement a new interface, like
`CharacterBuilderInterface`, which would have methods on it like `setMaxHealth()`,
`setBaseDamage()`, etc. This is *optional*. When would you *need* it? Well, like
all interfaces, it's useful if you need to be able to swap *how* your characters
are created for some other implementation.

For example, imagine we created a *second* builder that implemented
`CharacterBuilderInterface` called `DoubleMaxHealthCharacterBuilder`, which creates
the `Character` object, but in a *slightly* different way... like maybe it *doubles*
the `$maxHealth`. If both of those builders implemented
`CharacterBuilderInterface`, then inside of our `CharacterBuilderFactory`, which
would now *now* return `CharacterBuilderInterface`, we could read some configuration
to figure out which `CharacterBuilder` class we want to use.

So creating that interface really has less to do with the builder pattern itself...
and more to do with making your code more flexible. Let me undo that fake code inside
of `CharacterBuilderFactory`... and inside of `CharacterBuilder`, I'll remove that
make-believe interface.

## Where Do We See the Builder Pattern?

So do we see the builder pattern in the wild? This one is pretty easy to spot, since
method chaining is such a common feature of builders. The first one that comes to
mind is Doctrine's `QueryBuilder`, which (no surprise) builds queries! It allows
us to configure a query with a bunch of nice methods before finally calling
`getQuery()` to actually create the query object. It also leverages the factory
pattern: to create the builder, you call `createQueryBuilder()`. That method,
which lives on the base `EntityRepository` is the "factory" responsible for
instantiating the `QueryBuilder`.

Another example is Symfony's `FormBuilder`. In that case, we don't call the
`buildForm()` method, but *Symfony* eventually *does* call this once we're done
configuring it.

Ok team, let's talk about the *observer pattern* next.
