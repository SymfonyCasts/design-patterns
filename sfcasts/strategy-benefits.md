# Strategy Part 2: Benefits & In the Wild

We just used the Strategy Pattern to allow things *outside* of the `Character` class
to control *how* attacks happen by creating a custom `AttackType`... then passing
it in when you create the `Character`.

## Naming Conventions?

If you've read up on this pattern, you might be wondering why we didn't name the
interface `AttackStrategy` after the pattern. The answer is... because we don't *have*
to. In all seriousness, the clarity and *purpose* of this class are more valuable
than hinting the name of a pattern. If we called this "attack strategy"... it might
sound like it's responsible for actually *planning* a strategy of attack. That's
*not* what we intended. Hence our name: `AttackType`

[[[ code('1c5e5b3243') ]]]

## Another Strategy Pattern Example

Let's do one more quick strategy pattern example to further balance our characters.
I want to be able to control the armor of each character beyond just the number that's
being passed in right now. This is used down in `receiveAttack()` to figure out how
much an attack can be *reduced* by. This was fine before, but *now* I want to
start creating very different *types* of armor that each have different properties
beyond just a number. We'll need to upgrade our code to allow this.

[[[ code('04f42f69bf') ]]]

Once again, we *could* solve this by creating *sub-classes*, like
`CharacterWithShield`. But now you can hopefully see why that's not a great plan.
If we had also used inheritance for customizing how the attacks happen, we might
end up with classes like `TwoHandedSwordWithShieldCharacter` or
`SpellCastingAndBowUsingWearingLeatherArmorCharacter`. Yikes!

So rather than navigate that nightmare of never-ending sub-classes, we'll use the
*Strategy Pattern*. Let's revisit the three steps from earlier. Step one is to
*identify* the code that needs to change and create an interface for it.

In our case, we need to determine how much an attack should be reduced by. Cool:
create a new `ArmorType/` directory and inside that, a new PHP class... which
will actually be an interface... and call it, how about, `ArmorType`.

To hold the armor-reducing code, say `public function getArmorReduction()` where
we pass in the `$damage` that we're about to do, and will return how much damage
*reduction* the armor should apply.

[[[ code('f4c215f899') ]]]

Step two is to create at least one implementation of this. Create a new PHP class
called `ShieldType` and make it implement `ArmorType`. Below, I'll generate the
`getArmorReduction()` method. The shield is cool because it's going to have a 20%
chance to block an incoming attack *entirely*. Create a `$chanceToBlock` variable
set to `Dice::roll(100)`. Then, if the `$chanceToBlock` is `> 80`, we're going to
reduce *all* of the damage. So return `$damage`. *Else* our shield is going to be
meaningless and reduce the damage by zero. Ouch!

[[[ code('25ef037256') ]]]

While we're here, let's create two other types of armor. The first is a
`LeatherArmorType`. I'll paste in the logic: it absorbs 20% of the damage.

[[[ code('d7e2620448') ]]]

And *then* create the cool `IceBlockType`: a little something for our magic
folk. I'll paste that logic in as well. This will absorb two eight-sided dice
rolls added together.

[[[ code('67947bca1a') ]]]

Ok step three: allow an object of the `ArmorType` interface to be passed into
`Character`... then use its logic. In this case, we won't need the `$armor`
number at all. Instead, add a `private ArmorType $armorType` argument.

[[[ code('643bf3e1ef') ]]]

Down below, in `receiveAttack()`, say
`$armorReduction = $this->armorType->getArmorReduction()` and pass in `$damage`.
And just to make sure things don't drift negative, add a `max()` after `$damageTaken`
passing `$damage - $armorReduction` and `0`.

[[[ code('77d9d5dcee') ]]]

Done! `Character` now leverages the Strategy Pattern... again! Let's go take
advantage of that over in `GameApplication`.

Start by removing the armor number on each of these. Then I'll quickly pass in an
`ArmorType`: `new ShieldType()`, `new LeatherArmorType()`, and
 `new IceBlockType()`. For our `mage-archer`, which is our weird character, we'll
*keep it* weird by giving them a shield - `new ShieldType()`. That's a lot to carry!
Oh, and I also need to make sure I take off the armor for that as well. Perfect!

[[[ code('8abe1cfcc0') ]]]

Let's go try this team. Head over and run:

```terminal
./bin/console app:game:play
```

And... it looks like it's working! Let's play as a `mage-archer` and... sweet! Well,
I *lost*. That's *not* sweet, but I tried my best! And you can see that the "damage
dealt" and the "damage received" still seem to be working. Awesome!

## Pattern Benefits

So *that's* the Strategy Pattern! When do you *need* it? When you find that you need
to swap out just *part* of the code inside of a class. And what are the *benefits*?
A bunch! Unlike inheritance, we can now create characters with endless combinations
of attack and armor behaviors. We could also swap out an `AttackType` or `ArmorType`
at runtime. This means that we could, for example, read some configuration or
environment variable and dynamically use it to change one of the attack types of
our characters on the fly. That's not possible with inheritance.

## Pattern and SOLID Principle

If you watched our SOLID tutorial, the Strategy Pattern is a clear win for SRP -
the single responsibility principle - and OCP - the open closed principle.
The Strategy Pattern allows us to break big classes like `Character`
into smaller, more focused ones, but still have them interact with each other.
That pleases SRP.

And OCP is happy because we now have a way to modify or extend the behavior of the
`Character` class without actually *changing* the code inside. We can pass in new
armor and attack types instead.

## Strategy Pattern in the Real World

Finally, where might we see this pattern in the real world? One example, if you hit
"shift" + "shift" and type in `Session.php`, is Symfony's `Session` class. The
`Session` is a simple key value store, but different apps will need to store that
data in different locations, like the filesystem or a database.

Instead of trying to accomplish that with a bunch of code inside of the `Session`
class itself, `Session` accepts a `SessionStorageInterface`. We can pass
whatever session storage strategy we want. Heck, we could even use environment
variables to swap to a different storage at runtime!

Where else is the Strategy Pattern used? Well, it's subtle, but it's actually used in
a lot of places. Anytime you have a class that accepts an interface as a constructor
argument, especially if that interface comes from the *same* library, that's quite
possibly the Strategy Pattern. It means that the library author decided that, instead
of putting a bunch of code in the middle of the class, it should be abstracted into
another class. *And*, by type-hinting an *interface*, they're allowing someone
*else* to pass in whatever implementation - or *strategy* they want.

Here's another example. Over on GitHub, I'm on the Symfony repository. Hit "t"
and search for `JsonLoginAuthenticator`. This is the code behind the `json_login`
security authenticator. One common need with the `JsonLoginAuthenticator`
is to use it like normal... but then take control of what happens on success: for
example, to control the JSON that's returned after authentication.

To allow for that `JsonLoginAuthenticator` allows you to pass in an
`AuthenticationSuccessHandlerInterface`. So instead of *this* class trying to figure
out what to do on success, it allows *us* to pass in a custom implementation
that gives us complete control.

Think you've got all that? Great! Let's talk about the Builder Pattern *next*.
