# Builder in Symfony & with a Factory

What if, in order to instantiate the `Character` objects, `CharacterBuilder` needed
to, for example, make a *query* to the database? Well, when we need to make a query,
we normally give our class a constructor and then autowire the entity manager
service. But `CharacterBuilder` *isn't* a service. You could *technically* use it
like a service, but a service is a class where you typically only need a *single*
instance of it in your app. In `GameApplication` however, we're creating one
`CharacterBuilder` *per* character. If we *did* try to autowire `CharacterBuilder`
into `GameApplication`, that *would* work. Symfony *would* autowire the EntityManager
into `CharacterBuilder` and then it *would* autowire that `CharacterBuilder` object
here. The *problem* is that we would then only have *one* `CharacterBuilder`...
when we actually need *four* to create our four `Character` objects.

*This* is why builder objects are commonly partnered with a builder *factory*. Let
me undo all of the changes I just made to `GameApplication`... and `CharacterBuilder`.

## Creating a Factory

Over in the `Builder/` directory, create a new class called `CharacterBuilderFactory`:

[[[ code('3e60167997') ]]]

By the way, there *is* a pattern called the *factory pattern*, which we won't
*specifically* cover in this tutorial. But a "factory" is just a class whose job
is to create *another* class. It, like the builder pattern, is a *creational pattern*.
Inside of the factory class, create a new method called, how about...
`createBuilder()`, which will return a `CharacterBuilder`. And inside of *that*,
just `return new CharacterBuilder()`:

[[[ code('b5f993c014') ]]]

This `CharacterBuilderFactory` *is* a service. Even if we need *five*
`CharacterBuilder` objects in our app, we only need *one* `CharacterBuilderFactory`.
We'll just call this method on it *five* times.

That means, over in `GameApplication`, we can create a `public function __construct()`
and autowire `CharacterBuilderFactory $characterBuilderFactory`. I'll also add
`private` in front to make it a property:

[[[ code('143d0cd6f8') ]]]

Then, down inside `createCharacterBuilder()`, instead of creating this by
hand, rely on the factory: `return $this->characterBuilderFactory->createBuilder()`:

[[[ code('030b3e5ed4') ]]]

The nice thing about this factory (and this is really the *purpose* of the factory
pattern in general) is that we have *centralized* the instantiation of this object.

## Getting Services into the Builder

How does that help our situation? Remember, the problem I imagined was this:
What if our character builder needed a service like the `EntityManager`?

With our new setup, we can make that happen. I don't actually have Doctrine installed
in this project, so instead of the `EntityManager`, let's require
`LoggerInterface $logger`... and I'll again add `private` in front to turn that into
a property:

[[[ code('20943113c3') ]]]

Then, down in `buildCharacter()`, just to test that this is working, use it:
`$this->logger->info('Creating a character')`. I'll also pass a second argument
with some extra info like `'maxHealth' => $this->maxHealth` and
`'baseDamage' => $this->baseDamage`:

[[[ code('96b0798cd8') ]]]

`CharacterBuilder` now requires a `$logger`... but `CharacterBuilder` is *not* a
service that we'll fetch directly from the container. We'll get it via
`CharacterBuilderFactory`, which *is* a service. So autowiring `LoggerInterface`
will work here:

[[[ code('ca2d80546d') ]]]

Then, pass that *manually* into the builder as `$this->logger`:

[[[ code('bea49ea816') ]]]

We're seeing some of the benefits of the factory pattern here. Since we've already
centralized the instantiation of `CharacterBuilder`, anywhere that *needs* a
`CharacterBuilder`, like `GameApplication`, doesn't need to change at all... even
though we just added a constructor argument! `GameApplication` was already
offloading the instantiation work to `CharacterBuilderFactory`.

To see if this is working, run:

```terminal
./bin/console app:game:play -vv
```

The `-vv` will let us see log messages while we play. And... got it! Look! Our
`[info] Creating a character` message popped up. We can't see the other stats
on this screen, but they *are* in the log file. *Awesome*.

## What does The Builder Pattern Solve?

So *that's* the builder pattern! What problems can it solve? Simple! You have an
object that's difficult to instantiate, so you add a builder class to make life
easier. It also helps with the Single Responsibility Principle. It's one of the
strategies that helps abstract creation logic of a class *away* from the class that
will *use* that object. Previously, in `GameApplication`, we had the complexity
of both *creating* the `Character` objects *and* using them. We still have code
here to use the builder, but most of the complexity now lives in the builder class.

## Does my Builder Need an Interface?

Frequently, when you study this pattern, it will tell you that the builder
(`CharacterBuilder`, for example) should implement a new interface, like
`CharacterBuilderInterface`, which would have methods on it like `setMaxHealth()`,
`setBaseDamage()`, etc. This is *optional*. When would you *need* it? Well, like
all interfaces, it's useful if you need the flexibility to swap *how* your characters
are created for some other implementation.

For example, imagine we created a *second* builder that implemented
`CharacterBuilderInterface` called `DoubleMaxHealthCharacterBuilder`. This creates
`Character` objects, but in a *slightly* different way... like maybe it *doubles*
the `$maxHealth`. If both of those builders implemented
`CharacterBuilderInterface`, then inside of our `CharacterBuilderFactory`, which
would now *now* return `CharacterBuilderInterface`, we could read some configuration
to figure out which `CharacterBuilder` class we want to use.

So creating that interface really has less to do with the builder pattern itself...
and more to do with making your code more flexible. Let me undo that fake code inside
of `CharacterBuilderFactory`. And... inside of `CharacterBuilder`, I'll remove that
make-believe interface.

## Where Do We See the Builder Pattern?

And where do we see the builder pattern in the wild? This one is pretty easy to spot
because method chaining is such a common feature of builders. The first example
that comes to mind is Doctrine's `QueryBuilder`:

```php
class CharacterRepository extends ServiceEntityRepository
{
    public function findHealthyCharacters(int $healthMin): array
    {
        return $this->createQueryBuilder('character')
            ->orderBy('character.name', 'DESC')
            ->andWhere('character.maxHealth > :healthMin')
            ->setParameter('healthMin', $healthMin)
            ->getQuery()
            ->getResult();
    }
}
```

It allows us to configure a query with a bunch of nice methods before finally
calling `getQuery()` to actually create the `Query` object. It also leverages
the factory pattern: to create the builder, you call `createQueryBuilder()`.
That method, which lives on the base `EntityRepository` is the "factory"
responsible for instantiating the `QueryBuilder`.

Another example is Symfony's `FormBuilder`:

```php
public function buildForm(FormBuilderInterface $builder, $options)
{
    $animals = ['🐑', '🦖', '🦄', '🐖'];
    $builder
        ->add('name', TextType::class)
        ->add('animal', ChoiceType::class, [
            'placeholder' => 'Choose an animal',
            'choices' => array_combine($animals, $animals),
        ]);
}
```

In that example, *we* don't call the `buildForm()` method, but *Symfony* eventually
*does* call this once we're done configuring it.

Ok team, let's talk about the *observer pattern* next.
