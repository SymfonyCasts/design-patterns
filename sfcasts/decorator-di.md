# Decoration with Symfony's Container

We just implemented the decorator pattern, where we basically wrapped the original
`XpCalculator` in a warm hug with our `OutputtingXpCalculator`. Then... we quietly
slipped *that* into the system in place of the *original*... without anyone else -
like `XpEarnedObserver` - knowing or caring that we did that.

But to set up the decoration, I'm instantiating the objects *manually*, which
is not very realistic in a Symfony app. What we really want is for `XpEarnedObserver`
to autowire `XpCalculatorInterface` like normal, *without* us needing to do any of
this manual instantiation. But we need the container to pass it our
`OutputtingXpCalculator` decorator service, *not* the *original* `XpCalculator`.
How can we accomplish that? How can we tell the container that whenever someone
type-hints `XpCalculatorInterface`, it should pass our decorator service?

To answer that, let's start by undoing our manual code. In both `GameCommand`...
and then `Kernel`... put back the fancy code that attaches the observer to
`GameApplication`.

If we try the command now:

```terminal-silent
php bin/console app:game:play
```

It *fails*:

> Cannot autowire service `XpEarnedObserver`: argument `$xpCalculator` references
> interface `XpCalculatorInterface` but no such service exists. You should maybe
> alias this interface to one of these existing services: `OutputtingXpCalculator`
> or `XpCalculator`.

## Manually Wiring up the Service Decoration: Alias

That's a *great* error... and it makes sense. Inside of our observer, we're
type-hinting the *interface* instead of a concrete class. And, unless we do a
little more work, Symfony doesn't know *which* `XpCalculatorInterface` service to
pass us. How do we tell it? By creating a service *alias*.

In `config/services.yaml`, say `App\Service\XpCalculatorInterface` set to
`@App\Service\OutputtingXpCalculator`.

This creates a service whose id is `App\Service\XpCalculatorInterface`... but it's
*really* just a "pointer", or "alias" to the `OutputtingXpCalculator` service.
And remember, during autowiring, when Symfony sees an argument type-hinted with
`XpCalculatorInterface`, to figure out which service to pass, it simply looks in
the container for a service whose id matches that, so
`App\Service\XpCalculatorInterface`. And now, it finds one!

So, let's try it again.

```terminal-silent
php bin/console app:game:play
```

And... it *still* doesn't work. We're on a roll!

> Circular reference detected for service `OutputtingXpCalculator`,
> path: `OutputtingXpCalculator` -> `OutputtingXpCalculator`

Oh! Symfony is autowiring `OutputtingXpCalculator` into
`XpEarnedObserver`... but it's *also* autowiring `OutputtingXpCalculator` into
*itself*. Whoops! We want `OutputtingXpCalculator` to be used *everywhere* in the
system that autowires `XpCalculatorInterface`... *except* for itself.

To accomplish that, back in `services.yaml`, we can manually configure the service.
Down here, add `App\Service\OutputtingXpCalculator` with `arguments`,
`$innerCalculator` (that's the name of our argument) set to
`@App\Service\XpCalculator`.

This will override the argument for *just* this one case. And now...

```terminal-silent
php bin/console app:game:play
```

It work? I mean, of course it works! If we play a few rounds and fast forward...
yes! There's the "you've leveled up" message! It *did* go through our decorator!

This way of wiring the decorator is *not* our final solution. But before we get
there, I have an even *bigger* challenge: let's completely *replace* a core Symfony
service with our *own* via decoration. That's next!
