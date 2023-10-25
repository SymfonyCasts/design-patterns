# Builder Improvements

The first version of our builder class is done! Though, in `GameApplication`,
the `mage_archer` has *two* different attack types. Our `CharacterBuilder`
doesn't support that right now... but we'll add it in a minute.

## Clearing State After Building?

Oh, one more thing about the builder class! In the "build" method, after you create
the object, you *may* choose to "reset" the builder object. For example, we could
set the `Character` to a variable, then, before we return it, reset
`$maxHealth` and all the other properties back to their original state. Why would
we do this? Because it would allow for a *single* builder to be used over and over
again to create *many* objects - or, characters in this case.

[[[ code('b8abf2868c') ]]]

However, I'm *not* going to do that... which just means that a single `CharacterBuilder`
will be meant to be used just *one* time to build *one* character. You can choose
either option in your app: there isn't a right or wrong way for the builder pattern.

## Using the Builder

All right, let's go use this! Inside of `GameApplication`, first, just to make life
easier, I'm going to create a `private function` at the bottom called
`createCharacterBuilder()` which will return `CharacterBuilder`. Inside,
`return new CharacterBuilder()`.

[[[ code('f692613059') ]]]

That's going to be nice because... up here in `createCharacter()`, we can use that.
I'm going to clear out the old stuff... and now, use the fluid way to
make characters: `$this->createCharacterBuilder()`, `->setMaxHealth(90)`,
`->setBaseDamage(12)`, `->setAttackType('sword')` and `->setArmorType('shield')`.
Oh, and, though I didn't do it, it would be nice to add constants on the builder
for these strings, like `sword` and `shield`.

Finally, call `->buildCharacter()` to... *build* that character!

[[[ code('5df4a1e0a6') ]]]

That's really nice! And it would be even *nicer* if creating a character were even
*more* complex, like involving database calls.

To save some time, I'm going to paste in the other three characters, which
look similar. Down here for our `mage_archer`, I'm currently using the
`fire_bolt` attack type. We *do* need to re-add a way to have both `fire_bolt` *and*
`bow`, *but* this should work for now.

[[[ code('4d91597390') ]]]

Let's try it out! At your terminal, run:

```terminal
php bin/console app:game:play
```

Hey! It didn't explode! That's always a happy sign. And if I fight as an `archer`...
I win! Our app still works!

## Allow for Multiple Attack Types

So what about allowing our mage_archer's two attack types? Well, that's the *beauty*
of the builder pattern. Part of our job, when we create the builder class, is to
make life as *easy* as possible for whoever uses this class. That's why I chose to
use `string` `$armorType` and `$attackType` instead of *objects*.

We can solve handling *two* different `AttackTypes` however we want. Personally,
I think it would be cool to be able to pass multiple arguments. So let's make
that happen!

Over in `CharacterBuilder`, change the argument to `...$attackTypes` with an "s",
using the fancy `...` to accept any number of arguments. Then, since this will
now hold an *array*, change the property to `private array $attackTypes`... and
down here, `$this->attackTypes = $attackTypes`.

[[[ code('aea3d4c901') ]]]

Easy. Next we need to make a few changes down in `buildCharacter()`, like
changing the `$attackTypes` strings into objects. To do that, I'm going to
say `$attackTypes =` and... get a little fancy. You don't *have* to do this, but
I'm going to use `array_map()` and the new short `fn` syntax - `(string
$attackType) => $this->createAttackType($attackType)`. For the *second*
argument of `array_map()` - the array that we *actually* want to map - use
`$this->attackTypes`.

[[[ code('1c906efee4') ]]]

Now, in the private method, instead of reading the *property*, read an `$attackType`
argument.

[[[ code('891e62b0e6') ]]]

Ok, we *could* have done this with a `foreach` loop... and if you like `foreach`
loops better, *do it*. Honestly, I think I've been writing too much JavaScript lately.
Anyways, this basically says:

> I want to loop over all of the "attack type" strings and, for each one, call this
> function where we change that `$attackType` string into an `AttackType` object.
> Then set all of those `AttackType` objects onto a *new* `$attackTypes` variable.

In other words, this is now an *array* of `AttackType` objects.

To finish this, say `if (count($attackTypes) === 1)`, *then*
`$attackType = $attackTypes[0]` to grab the first and only attack type. Otherwise,
say `$attackType = new MultiAttackType()` passing `$attackTypes`. Finally, at
the bottom, use the `$attackType` variable.

[[[ code('6e376e40ab') ]]]

Phew! You can see it's a bit ugly, but that's okay! We're hiding the creation
complexity *inside* this class. And we could easily unit test it.

Let's try things out. Run our command...

```terminal-silent
./bin/console app:game:play
```

... let's be a `mage_archer` and... awesome! No error! So... I'm going to assume
that's all working.

Ok, in `GameApplication`, we're instantiating the `CharacterBuilder` *manually*.
But what if the `CharacterBuilder` needs access to some services to do its job,
like the EntityManager so it can make database queries?

Next, let's make this example more useful by seeing how we handle the creation of
this `CharacterBuilder` object in a *real* Symfony app by leveraging the service
container. We'll also talk about the *benefits* of the builder pattern.
