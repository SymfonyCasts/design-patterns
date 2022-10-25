# Improving our Builder

Coming soon...



Over in `GameApplication.php`, we do have a case of the `mage_archer`, which has
*two* different attack types. Our `CharacterBuilder` doesn't support that right now,
but we'll add that in a second. And for the strings that we're about to pass in, it
would also be a great idea to put constants on this class.

Oh, one last thing! In the build method of a builder, after instantiating the object,
it *may* reset itself sometimes. For example, if we set this to a variable, then
before we actually encounter the *real* return statement, we would set the max health
back to zero so that we could use this class over and over again. I'm *not* going to
do that, which just means that a `CharacterBuilder` is meant to be used just *one*
time to build *one* character.

All right, let's go use this! Inside of `GameApplication.php`, first, just to make
life a little easier, I'm going to create a `private function` at the bottom called
`createCharacterBuilder()` which will return `CharacterBuilder`. And below, we'll say
`return new CharacterBuilder()`. That's going to be nice because, up here in
`createCharacter()`, we can use that. I'm going to clear out the old stuff in here...
and now, we have a nice, fluid way to do this. Say `$this->createCharacterBuilder`,
`->setMaxHealth(90)`, `->setBaseDamage(12)`, `->setAttackType('sword')` (that's where
you could use a constant if you wanted to), and `->setArmorType('shield')`. And
*finally*, say `->buildCharacter()` to *build* that character. That's really nice!
And it would be even *nicer* if creating a character were even *more* complex, like
involving database calls.

Okay, to save us a little time here, I'm going to paste in the other three
characters, which look very similar. Down here, for our `mage_archer`, I'm currently
using the `fire_bolt` attack type. We do need to re-add a way to have both
`fire_bolt` *and* `bow`, *but* this should work for now. Let's test it out! At your
terminal, run:

```terminal
./bin/console app:game:play
```

It doesn't explode! And if I fight as an `archer`... I win! Our app still works!

So what about allowing our `mage_archer` two attack types? Well, that's the *beauty*
of the builder pattern. Part of our job, when we create the builder class, is to make
life as easy as possible for whoever uses this class. That's why I chose to use
`string` `armorType` and `attackType` instead of *objects*. It just makes it *easier*
when you actually use this class.

We can solve handling *two* different `AttackTypes` however we want, but the *most*
user-friendly way to do this just might be the builder. Personally, I think it would
be cool to just be able to do *that*. So let's make it happen! Over in
`CharacterBuilder`, I'm going to take change this to `...$attackTypes` with a "s" and
using the fancy rest notation to accept any number of arguments. Then, since we
really will have an array of `$attacktypes`, I'll change the property to `private
array $attackTypes`... and down here, `$this->attackTypes = $attackTypes`. *Easy*.

Now I need to make a couple of changes down in `BuildCharacter`. The first thing I
want to do is change the `$attackTypes` strings into proper `$attackTypes` objects.
To do that, I'm going to say `$attackTypes =` and I'm going to get a little fancy
here. You don't *have* to do this, but I'm going to use `array_map()` and then the
new short `fn` syntax - `(string $attackType) => $this->createAttackType()`, passing
in `$attackType`. For the *second* argument of `array_map()` - the array that we
*actually* want to map - I'm going to pass in `$this->attackTypes`. Whew... Before I
explain that, instead of *reading* the property, we're just going to read an
`$attackType` argument, so let me change that down here.

I *could* have done this with a foreach loop, and if you like foreach loops better,
*do it*. This basically says:

`I want you to loop over all of the $attackType strings and for each1, call this
function where we change the $attackType string into an $attackType object. Then set
all of those $attackType objects onto a *new* $attackType variable, so this is now an
*array* of $attackType objects.`

An easier way to handle the rest of this is to say `if (count($attackTypes) === 1)`,
*then* `$attackType = $attackTypes[0]`. And it will just grab the `1` there, meaning
we have a sword, for example. Otherwise, we'll say `$attackType = new
MultiAttackType()`. And then, we'll pass in the attack types down here. Instead of
calling `$this->createAttackType()`, we'll just pass that the `$attackType` variable.
You can see it's get a little bit ugly in here, but that's okay, because it makes
this class *very* user-friendly.

All right, let's try it out. I'll run our command... let's be a `mage_archer` and...
awesome! No error! So... I'm going to assume that worked. In `GameApplication.php`,
we are instantiating the `CharacterBuilder` *manually*. But what if the
`CharacterBuilder` needs access to some service inside of it, like the EntityManager
so it can make some database queries?

Next, I want to make this example a little more useful by seeing how we handle the
creation of this `CharacterBuilder` object in a real Symfony app, leveraging the
service container. We'll also talk about the *benefits* of the builder pattern.
