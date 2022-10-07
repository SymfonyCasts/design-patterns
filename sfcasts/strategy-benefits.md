# Strategy Part 2: Benefits & In the Wild

Coming soon...

We just use the strategy pattern to allow things outside of the character glass to
control how attacks happen by creating a custom attack type and then passing it in
when you create the character it. Now, if you've read up on this pattern, you might
be wondering why we didn't name the interface attack strategy after the strategy
pattern, which you often see when studying this pattern. The answer is because I
don't have to. But really clarity and purpose of this class are much more valuable
than hinting the name of a pattern. In this case, if we call this attack strategy, it
might sound like it's responsible for actually planning a strategy of attack, not
what we intended, hence attack type. All right, let's do one more quick strategy
pattern example to further balance our characters. I want to be able to control the
armor of each character beyond just the number that's being passed in right now. This
is used down in receive attack to figure out how much the attack can be reduced by,
but in our case, we're going to start creating very different types of armor like
shield armor or leather armor that have very different properties beyond just a
number. So once again, we could solve this by creating subclasses like character with
shield, which you can really see now why that's not a good plan. If we had used
inheritance

For customizing how the attacks happen, we might start having classes like sword with
2, 2, 2 handed sword with shield character or or spell casting and bow using Wearing
leather armor character. We need one subclass per combination. Yikes. All right, so
let's use the strategy pattern and we'll go back to the three steps from earlier.
Step one is to identify the code that needs to change the code that needs to change.
In our case, figuring out how much an attack should be reduced by in creating an
interface for that.

So

Let's create a new attack type directory armor type directory. And inside there I'll
create a new PHP class, which will actually be an interface and we'll call this armor
type in this case to model that code. We'll say public function, get armor reduction
where we pass in the damage that we're about to do, and we'll return how much damage
reduction the shield, the armor should do. All right, Step two is to create at least
one implementation of this. So for example, let's create a new peach of glass called
shield type, and we'll make it implement armor type. I'll generate the get armor
reduction method. And the shield is cool because it's going to have a 20% chance to
block the attack entirely. So I'll create a chance to block variable set to a 100
sided dice roll. And then we'll say if the chance to block is greater than 80, then
we are going to reduce all of the damage. So return damage else, our shield is going
to be meaningless and reduce the damage by zero. Now, once again, while we're here,
I'm going to create two other types, a leather armor type. Now I'll paste in the
logic there just for 20% damage, absorb, absorption,

And then we'll create a cool ice block, a type for our magic folk. I'll face that in.
And this will absorb two eight sided dice rolls added together. All right, now to
step three to allow an object of the armor type interface to be passed into
character. And then you use its logic. So in this case, we won't need this armor
number at all. We're going to be more intelligent than that. And I'll now add a new
argument called private armor type armor type. And then down below in receive attack,
we'll say armored reduction = this error armor type-> get armor reduction and we'll
pass in damage. And just to make sure things don't go negative here, I'll add a max
for damage taken between damage minus armor reduction and zero and done. Our
character now leverages the strategy pattern for the armor type, which means we can
take advantage of it over in game application. So first I'll do is remove the armor
number that we had on all of those. Now at the end here for the fighter. Then I'll
quickly pass in an armor type for each of these new shield type new

Leather

Armor type new ice block type. And for our major archer, which is kind of our weird
character, we'll keep it weird by giving that person a shield. That's a lot to carry
open. I need to make sure I also took off the armor number for it. Perfect. All
right, let's try this. Head over to our head over and run bin console game app game
play, and looks like it's working. Let's play as a major archer and sweet. Well, I
lost. That's not sweet, but you can, The damage down. Damage receive still seems to
be working. Awesome. So that is the strategy pattern. When do you need it? When you
find that you want to swap out just part of the code inside of a class, and what are
the benefits? A bunch. Unlike inheritance, we can now create characters with all
sorts of mixed attack and armor behaviors. Also, unlike inheritance, we could swap
out an attack type or armor type at run time. What that means is we could, for
example, read some configuration or even some environment variable and dynamically
use it to change one of the attack types of our characters on the fly. That's simply
not possible with inheritance.

All right, what else? Well, if you watched our solid tutorial, the strategy pattern
is a clear win for ocp, for S R P N O C P S R P, the single responsibility pattern,
the strategy pattern allows us to break big classes like character into smaller, more
focused ones, but then still have them interact with each other and ocp, the open
closed principle is happy because we now have a way to modify or extend the behavior
of the character class without actually changing the character class, packing,
passing a new attacks in armors. We're effectively changing code inside of here
without having to physically change code inside of here. All right, finally, where do
we see this pattern in the wild, in the real world? One example, if you hit shift,
shift and type in session, PHP is Symfony session class. The session is a simple key
value store, but different apps will need to store that data in different locations.
Instead of trying to accomplish that with a bunch of code inside of the session class
itself, the session class accepts a session storage interface. We can pass whatever
session storage strategy we want. Heck, we could even use environment variables

To swap a different storage at run time. All right, where else is the strategy
pattern used? Well, it's subtle, but it's kind of used anywhere. Anytime that you
have a class that accepts an interface as a constructive argument, especially if that
interface comes from the same library, that's quite possibly the strategy pattern. It
means that the library author decided that instead of

Putting

A bunch of code in the middle of the class, it should be abstracted into another
class. And by type hinting the interface, someone can swap out the implementation. In
this case, these session storage for any implementation. Here's another example over
on GitHub, I'm on the Symfony repository. I'm going to search for login
authenticator, which is the code behind the JSON_login way of logging in with
Symfony.

One common thing that you want to do with the JSON login authenticator is use it like
normal, have it do all the normal work, but then you control what happens on success,
for example, to control what JSON is returned from your application. After you log in
to allow for that, the JS login authenticator allows you to pass in an authentication
success handler. So instead of this class trying to figure out what to do on success,
it allows us to pass in a custom implementation of this that completely controls what
to do on success. All right, next up, let's talk the builder pattern.
