# Decoration with Symfony's Container

We just implemented the decorator pattern, where we effectively wrapped the original
`XpCalculator` with our `OutputtingXpCalculator`... then quietly slipped that into
the system in place of the original without anything else - like `XpEarnedObserver` -
knowing or caring that we made that change.

But to set up the decoration, I'm instantiating the objects *manually*, which
is not very realistic in a Symfony app. What we really want is for `XpEarnedObserver`
to autowire `XpCalculatorInterface` like normal, *without* us having to do any of
this manual instantiation. But we need it to pass us our `OutputtingXpCalculator`
decorator service, not the original `XpCalculator`. How can we accomplish that?
How can we tell the container that whenever someone type-hints
`XpCalculatorInterface`, it should pass us our decorator service?

To answer that, let's start by undoing our manual code. In both `GameCommand`...
and then `Kernel`... put back the code that attaches the observer to
`GameApplication`.

If we try it the command now:

```terminal-silent
php bin/console app:game:play
```

It *fails*:

> Cannot autowire service `App\Observer\XpEarnedObserver`: argument
> `$xpCalculator` references interface `App\Service\XpCalculatorInterface` but no
> such service exists. You should maybe alias this interface to one of these existing
> services: `OutputtingXpCalculator` or `XpCalculator`.

## Manually Wiring up the Service Decoration: Alias

This makes sense. Inside of our observer, we're type-hinting the *interface* instead
of a concrete class. And, unless we do a little more work, Symfony doesn't know which
`XpCalculatorInterface` service to pass us. How do we tell it? By creating a
service *alias*.

In `config/services.yaml`, say `App\Service\XpCalculatorInterface` set to
`@App\Service\OutputtingXpCalculator`.

This creates a service called `App\Service\XpCalculatorInterface` that's actually
just a pointer to the `OutputtingXpCalculator` service. And, remember, during
autowiring, when Symfony sees an argument type-hinted with `XpCalculatorInterface`,
to figure out which service to pass, it simply looks in the container for a service
whose id is `App\Service\XpCalculatorInterface`. And now, there is one!

So, let's try it again. And... it *still* doesn't work:

> Circular reference detected for service `App\Service\OutputtingXpCalculator`,
> path: `App\Service\OutputtingXpCalculator` -> `App\Service\OutputtingXpCharacter`

That makes sense too! Symfony is autowiring `OutputtingXpCalculator` into
`SpEarnedObserver`... but it's *also* autowiring `OutputtingXpCalculator` into
*itself*.
Whoops! We want `OutputtingXpCalculator` to be used *everywhere* in the system
that autowires `XpCalculatorInterface`... *except* for itself.

To accomplish that, back in `services.yaml`, we can manually configure the service.
Down here, add `App\Service\OutputtingXpCalculator` with `arguments`,
`$innerCalculator` (that's the name of our arguments) set to
`@App\Service\XpCalculator`.

This will override the argument for *just* this one case. And now...

```terminal-silent
php bin/console app:game:play
```

It seems to work! If we play a few rounds and fast forward... yes! There's the
"you've leveled up" message! It *did* go through our decorator.

This way of wiring thee decorator is *not* our final solution. But before we get
there, I have an even *bigger* challenge: let's completely *replace* a core Symfony
service with our *own* via decoration. That's next!
