# Strategy Pattern

Coming soon...

The first pattern we'll talk about is the strategy pattern, which is one of those
behavioral patterns, the kind that help you organize code into separate classes that
can then interact with each other. Let's start with the technical definition. The
strategy pattern defines a family of algorithms, encapsulates each1 and makes them
interchangeable. It lets the algorithm vary independently from clients that use it.
Oh, if that made sense to you, congrats. You should probably be teaching this course
instead of me. So let's try that again. Here's my definition. The strategy pattern is
a way to allow part of a class to be controlled from the outside. Let's look at an
imaginary example before we start coding. Suppose we have a payment service that does
a bunch of stuff, including charging people via the credit card, but now we want to
be able to use, sometimes use this exact same class except charge people via PayPal.
Instead, how can we do that? The strategy pattern. Allow some new payment strategy
interface object to be passed into payment service and just call that. Then create
two classes that implement this credit card payment strategy and PayPal payment
strategy.

You now have control which you pass in. Yep. You just made part of the code inside
payment service controllable by outside code. Cool. Now that we've got a quick
preview, let's code a real example. Right now we have three characters that are
created inside of game application, but the fighter is dominating. To balance the
game, we're going to, we want to add special attack abilities to each character. For
example, the MA will be able to cast spells, which unlike the normal attack
functionality, which is inside of character, where you just take the base damage plus
the normal attack functionality is pretty boring. We take the base damage for the
user and then we use this cool dice roll, which is going to roll a six-sided dice for
some randomness. But when a MA cast a spell, that's going to have a much higher, uh,
random variability that could do a lot more damage. So we basically need completely
different, uh, code for when calculating the amount of damage to do so. How could we
allow the MA to cast spells? Well, the first idea that comes to mind is to pass some
flag into the character's constructor, like can cast spells and then in the attack
method, add an if statement so that we have the two different types of attacks.

But then what if an archer will need get a different type of attack? Then we'll have
to pass another flag into here and then have three variations inside of attack. You
can kind of see the problem. Okay, so another solution is that we could create a
subclass of character. We could create like a major character and then override the
attack method entirely. But darn it, we don't want to override all of attack, we just
want to replace part of it. Yes, we could get fancy by moving the part we want to
reuse into a protected function. So we could call that from our subclass, but this is
just getting a little ugly. And whenever we can solve a problem without inheritance,
that's a good idea. So backing up again, what I really want to be able to do here is
just change this code for the, have this code be different for a MA versus the other
characters on a character by character basis. And that is what the strategy pattern
allows us to do. Exactly. All right, so let's do this. The logic we want to be able
to change is just this part here where we determine how much an attack did. So step
one to this pattern is to create an interface that describes this work.

So I'm going to create a new attack type directory to organize things, and inside of
there I'll got a newb class, pop it over to interface. There we go. And we'll call it
attack type.

It's

Cool. And instead of here, let's create one public function called perform attack.
And what this will do is it will will pass in the base damage, this will perform the
attack and then return the final damage that should be um, applied. All right, cool.
Step two is to create at least one implementation of this interface. So for our ma,
let's pretend that they have this cool fire attack. So inside the same directory,
I'll create a fire bolt type and we'll have this implement attack type. I'll go down
here, I'll go to code generate or command N on a Mac and go to "Implement Methods" as
a shortcut to build our method inside of here. I'm going to use that dice roll class
three times. So dice roll 10 means you roll 10 sided dice. So we're actually going to
roll a 10 sided dice three times our first attack type is done. And while we're here,
let's create two other attack types. I'll create a bow type and I'm actually just
going to paste in the code here. So this is a chance of doing some critical damage
and then a two handed sword type. I'll also just paste in that code.

And this one is pretty straightforward. It's the base damage plus some random roles.
Cool. All right. The third and final step is to allow an attack type interface object
to be passed into character via its construction so that we can use it down below. So
quite literally we're going to add a new argument here called private that's also a
property type in with the attack type interface. So we can allow any attack type we
passed in and we'll call it attack type. And down here I'll take remember this
comment because now instead of doing the logic manually, we'll say return
this->attack type->perform attack. This->based damage passed in and done. Our
character class is now leveraging this, the strategy pattern. It allows someone
outside of this class to pass in an attack type object effectively letting them
control just part of the code inside of the our class to take advantage of the
pattern over in open up a game application and inside of great character, we're going
to pass in the attack type for each of these. So we'll say new two handed sword
attack for the fighter, new bow type attack for the archer and new fire bolt type for
our ma

Sweet to make sure we didn't break anything. Let's go over and try our app. And
sweet. Looks like it's still working. What's great about the strategy pattern is that
instead of trying to pass some options to character, like can cast spells = true to
control how an attack happens, we have full control. We can do absolutely anything we
want inside of these classes to prove it. I want to add a new character, a major
archer, a legendary character that has a bow and can cast spells to support this.
This idea of having two attacks, we're just going to create a new attack type. I'm
going to call it multi attack type. Ooh, we'll make it implement our attack type
interface. I'll implement the performed T method, and then in this case I'm going to
create a constructor where we can pass in a an array of attack types. And to help out
my editor, I'm going to put a little PHP doc above us so that knows this is an array
specifically of attack type objects. Then down here, the way this character works is
it will randomly choose between one of its available attack types. So I'll say type =
$this->

Attack type. Oh, I'm, I'm meant to call this attack types plural. There we go. And
then array rand, this era attack types. So that funny looking line there will give us
a random type and we can say return type arrow. Perform attack based damage. So this
is really crazy. We're not leveraging multiple attack types inside of attack type.
It's very custom, but the strategy pattern allows us to do whatever we want over
inside game application. We'll now add our new major archer

And I'll copy the code above. Let's have this be, let's say about 75 9 0.15. And then
for the attack type, it's going to be new multi attack type. And here as we pass in
new bow type and new fireball type sweet. And in this app, we also need to update our
little get character list down there so that it shows up in our selection list when
we run it. All right, let's try it. See how legendary this may Archer is. I'll select
May Archer and oh stunning victory against a normal archer. We've got it. How cool is
that? All right. Next I'm going to use the strategy pattern one more time to make our
character class even more flexible. Then we'll talk about where you see the strategy
pattern in the wild, and specifically what benefits you get from it.

