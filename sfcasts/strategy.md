# Strategy Pattern

The first pattern we'll talk about is the "strategy pattern". This is a *behavioral*
pattern that helps organize code into separate classes that can then interact
with each other.

## Definition

Let's start with the *technical* definition:

> The strategy pattern defines a family of algorithms, encapsulates each one and
> makes them *interchangeable*. It lets the algorithm vary independently from clients
> that use it.

If that made sense to you, congrats! *You* get to teach the rest of the tutorial!

Let's try that again. Here's *my* definition:

> The strategy pattern is a way to allow *part* of a class to be rewritten from
> the *outside*.

## Imaginary Example

Let's talk about an *imaginary* example before we start coding. Suppose we have a
`PaymentService` that does a bunch of stuff... including charging people via credit
card. But now, we discover that we need to use this *exact* same class to charge
people via PayPal... or pirate treasure - that sounds more fun.

Anyways, how can we do that? The *strategy pattern*! We would allow a new
`PaymentStrategyInterface` object to be passed *into* `PaymentService` and then
we would just call *that*.

Next, we would create two classes that *implement* the new interface:
`CreditCardPaymentStrategy` and `PiratesBootyPaymentStrategy`. That's it! *We* now
have control of *which* class we pass in. Yep! We just made part of the code *inside*
`PaymentService` controllable from the *outside*.

## The Real Example

With that in mind, let's actually *code* this pattern.

Right now, we have three characters that are created inside of `GameApplication`.
But the `fighter` is *dominating*. To balance the game, we want to add special attack
abilities for each character. For example, the `mage` will be able to *cast spells*.

Currently, the attack functionality is pretty *boring*: we take the character's
`baseDamage` then use this cool `Dice::roll()` function to roll a six-sided die
for some randomness.

But when a `mage` casts a spell, the damage it causes will be *much* more variable:
sometimes a spell work really well but... other times it makes like tiny fireworks
that do almost zero damage.

Basically, for the mage, we need *completely* different code for calculating
damage.

## Pass in an Option?

So how can we do this? How can we allow *one* character - the mage - to have
different damage logic? The first idea that comes to *my* mind is to pass a flag
into the character's constructor, like `$canCastSpells`. Then in the `attack()`
method, add an `if` statement so that we have both types of attacks.

Cool... but what if an `archer` needed yet a *different* type of attack? We'd then
have to pass *another* flag and we'd end up with *three* variations inside of
`attack()`. Yikes.

## Sub-Class?

Ok then, another solution could be that we sub-class `Character`. We create a
`MageCharacter` that extends `Character`, then override the `attack()` method
entirely. But, darn it! We don't want to override *all* of `attack()`, we just want
to replace *part* of it. We *could* get fancy by moving the part we want to reuse
into a protected function so that we can call it from our sub-class... but this is
getting a little ugly. Ideally we can solve problems *without* inheritance whenever
possible.

## Creating the "strategy" Interface

So let's back up. What we *really* want to do is just allow this code to be different
on a character-by-character basis. And that is *exactly* what the strategy pattern
allows.

Let's do this! The logic that we need the flexibility to change is this part here,
where we determine how much damage an attack did.

Ok, step 1 to this pattern is to create an interface that *describes* this work.
I'm going to add a new `AttackType/` directory to organize things. Inside,
create a new PHP class, change the template to "Interface", and call it
`AttackType`.

Cool! Inside, add one `public function` called, how about, `performAttack()`. This
will accept the character's `$baseDamage` because that might be useful, then return
the final damage that should be applied.

Awesome!

## Adding Implementation of the Interface

Step 2 is to create at least one implementation of this interface. Let's pretend
our `mage` has a cool fire attack. Inside the same directory, create an class
called `FireBoltType`... and make this implement `AttackType`. Then, go to
"Code -> Generate" - or "command" + "N" on a Mac - and select "Implement Methods"
as a shortcut to add the method we need.

For this magic attack, return `Dice::roll(10)` 3 times. So the damage done is
the result of rolling 3 10-sided dice.

And... our first attack type is done! While we're here, let's create two *others*.
I'll add a `BowType`... and paste in some code. You can copy this from the code
block on this page. This attack has a chance of doing some *critical* damage.
Finally, add a `TwoHandedSwordType`... and I'll paste in that code as well. This
one is pretty straightforward: it's the `$baseDamage` plus some random rolls.

## Passing in and Using the Strategy

We're ready for the 3rd and final step fro this pattern: allow an `AttackType`
interface to be passed into `Character` so that we can use it below. So, quite
literally, we're going to add a new argument here: `private` - so it's also a
property - type-hinted with the `AttackType` interface (so we can allow any `AttackType`
to be passed in) and call it `$attackType`.

Below,remove this comment... because now, instead of doing the logic *manually*,
we'll say `return $this->attackType->performAttack($this->basedDamage)`.

And we're done! Our `Character` class is now leveraging the *strategy* pattern.
It allows someone *outside* of this class to pass in an `AttackType` object,
effectively letting them control just *part* of its code.

## Taking Advantage of our Flexibility

To take advantage of the new flexibility, open up `GameApplication`, and inside of
`createCharacter()`, let's pass an `AttackType` to each of these. Say `new
TwoHandedSwordType()` for the `fighter`, `new BowType()` for the `archer`, and
`new FireBoltType()` for the `mage`.

Sweet! To make sure we didn't break anything, let's go over and try our app.

```terminal-silent
php bin/console app:game:play
```

And... woohoo! It's *still* working!

## Adding a Mixed Attack Character

What's great about the "strategy pattern" is that, instead of trying to pass options
to `Character` like `$canCastSpells = true` to configure the attack, we have *full*
control.

To prove it, I want to add a new character - a *mage archer*: a legendary character
that has a bow *and* casts spells. Dang, they sound sweet!

To support this idea of having *two* attacks, create a new `AttackType` called,
how about, `MultiAttackType`. Make it implement the `AttackType` interface
go to "Implement Methods" to add the `performAttack()` method.

In *this* case, I'm going to create a constructor where we can pass in an `array`
of `$attackTypes`. To help out my editor, I'll add some PHPDoc above to note that
this is an array specifically of `AttackType` objects.

This class will work by randomly choosing between one of its available `$attackTypes`.
So, down here, I'll say `$type = $this->attackTypes[]` - whoops! I meant to call this
`attackTypes` with a "s" - then `array_rand($this->attackTypes)`. Then
`return $type->performAttack($baseDamage)`.

Done! This is a *very* custom attack, but with the "strategy pattern", it's no
problem. Over in `GameApplication`, add the new `mage_archer` character... and I'll
copy the code above. Let's have this be... `75, 9, 0.15`. Then, for the `AttackType`,
say `new MultiAttackType([])` passing `new BowType()` and `new FireBoltType()`.

Sweet! Below, we also need to update `getCharacterList()` so that it shows up in
our character selection list.

Okay, let's check out the *legendary* new character:

```terminal-silent
php bin/console app:game:play
```

Select `mage_archer` and... oh! A *stunning* victory against a normal `archer`.
How cool is that?

Next, let's use the "strategy pattern" one more time to make our `Character`
class even *more* flexible. Then, we'll talk about where you can see the "strategy
pattern" in the wild and what *specific* benefits it gives us.
