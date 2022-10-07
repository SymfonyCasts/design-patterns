# Builder Pattern

Coming soon...

Time for design pattern number two, the builder pattern.
This is one of those creation patterns. Patterns that help you instantiate and
configure objects. And it's a bit easier to understand than the strategy pattern.
Okay, official definition time. The builder pattern is a creation design pattern that
lets you build and configure object complex objects step by step. Oh, that kind of
made sense. Part two of the official definition is the pattern allows you to produce
different types and representations of an object using the same construction code. In
other words, the builder pattern is where you create builder class that helps you
build other objects, which might be of different classes or just the same class with
different data. A simple example might be a pizza store that needs to create a bunch
of pizzas, each which have different toppings, cheese, et cetera. To make life
easier, the owner of the pizza store who is also a Symfony developer by night
decideds to create a pizza builder class with easy methods like add ingredient, set
dough and add cheese, then a build pizza method, which takes all of that info and
does the heavy lifting of creating that pizza object and returning it. That build
pizza method can get as ugly and complex as needed. Anyone using this class to build
a pizza doesn't know or care about any of that. All right, let's code up a builder in
our app.

The problem right now is that inside of game application down here and create
character, we're building four different character objects and passing already quite
a bit of data to configure each1 of these is suppose that we might need to create
these character objects in other places in our code. They're not super easy to create
right now. And yes could create some subclass to character that set this stuff up
automatically. But like we talked about with the strategy pattern, that could get
really ugly when we start having things like major archer with ice block shield type
of classes. And what if creating a character class object were even more difficult
like it required making queries to the database or other operations. So the goal is
to make the instantiation of the character objects easier and more clear. And we'll
do that by creating a builder class. Let's create a source builder directory just for
organization inside of there, a new PHP class called Character builder. I'm creating
this class but I am not creating a corresponding interface. These builder classes
often implement a new interface like character builder interface, but they don't need
to. Later we'll talk about why you might decide to add an interface in some
situations.

Okay, Now inside this class, we get to just create whatever methods that the outside
world will need to call on this in order to craft their new character. So for
example, public function set max health, which will accept an in max health argument.
And I'm going to leave this method empty for a second. We'll fill it in. And I'm also
eventually going to have this return itself. So Xa going to return its own character
builder. This is really common in the builder pattern because it allows method
shaming, but it's not a requirement of the pattern. All right, let's fill in a couple
more methods really quickly. So we're also going to allow to set the base damage. And
then the last two things are the armor type and the the attack type and the armor
type. So we'll say attack type. Now remember, attack types are an object, but instead
of allowing like an attack type interface right here, I'm going to make this a string
argument called attack type. Why? Well, I don't have to do it this way, but I'm
trying to make it at as easy as possible to create characters. So instead of making
somebody else instantiate the attack types, I'm going to allow them to pass in a
simple string like the word bow. And then we will in a few minutes actually handle
the complexity of instantiating the the object for them.

All right? So I'll copy that. We do the set thing, same thing for a set armor type
and that's it. Those are the only four things that you can really control in a
character. So the last method that our builder needs is the method that that's
actually going to build the character. This can be called anything you want. I'll
call it build character. And it is of course going to return a character object.
Okay? Now to store the kind of preferences of the user as they call things, we're
going to create four properties. I'll just paste these in, into max health and base
damage, and then string attack type and armor type. And then really quickly in the
each method, how assign that property and then return this. So we'll do that for base
damage, T type and arm type. Beautiful. Now build character is fairly
straightforward. We do whatever ugly work we need to do to create the character. So
I'll say return new character. And the first two arguments of this are pretty simple.
We can say this->max health, this->based damage. And then the last two things is, is
actually where we need those attacked the type objects. So this is a little bit more
complex

And that's okay. I don't mind if my builder gets a little complex. I'm actually going
to go to the Bombas class and paste in two new private methods that will handle
creating an attack type and the armor type, except I need a bunch of youse statements
for this, I forgot. So I'm going to retype really quickly, bunch of classes and hit
tab to get the use statements for those. There we go. So two private methods. So
we're just going to map that attack type and then our type to the special objects. So
this is the heavy lifting. This is the value right now of our character builder. So
here, and I can just say this->create a tact type and this->create arm type and done.
We are skipping <affirmative> over in game application. We do have a case of the
major archer, which has two different attack types. Our character builder doesn't
support that right now, but we'll add that in a second. And yes, for these strings
that we're about to pass in, we could also put constants on this class. That would
agree a great idea. Oh, one last thing. Sometimes in these build methods in the build
method of a builder,

After creating, after instantiating the object, it might reset itself. So for
example, if we set this to a variable, then before we actually had the real return
statement, we would, you know, kind of set max health back to zero so that you could
use this class over and over again. I'm not going to do that, which just means that a
character builder is meant to be used just one time to build one character. All
right, let's go use this inside of game application first, just to make life a little
easier, I'm going to create a private function at the bottom called create character
builder

Or

Return a character builder. And very simply it will say Return new character builder.
That's going to be nice because up here in Create character, we can use that. So I'm
going to clear out the old stuff here. And now with this really nice fluid way to do
this, we could say this->create character builder,->set max health 90 set base
damage, 12 set attack type. Just this nice string sword. That's where you could use a
constant if you wanted to. And set armor type shield yield. And finally build
character to build that character. That's really nice. And it would be even nicer if
creating a character were even more complex like it involved database calls. All
right, kinda save us a little of time here. I'm going to paste in the other three
characters which look exactly the same way. The only thing we haven't done is down
here, the ma archer. I'm using the fire bolt attack type. We need to re-add a way so
we can have fire bolt and bow, but this should work. Let's test it out real quick.
Pin console, app game play. It doesn't explode. And if I fight as an archer,

I win, Our app still works. Okay, so what about a allowing this fire bolt and bow
thing to attack types? Well, that's the beauty of the builder pattern. Part of our
job when we create the builder class is to try to make life as easy as possible for
whoever is using this class, which is why I chose to use string armor and attack type
instead of objects. It just makes it easier when you actually use this class. So to
handle two different attack types, we can solve this however we want. However we
think might be the most user friendly when you're using the builder. Personally, I
think it would be kind of cool to just be able to do that. Cool. So let's make that
possible over in character builder. I'm going to take change this to attack types and
use the fancy rest notation. I need to look that up to allow us to take any number of
arguments. And then since we really are going to have an array of attack types, now
I'll say I'll change the uh, properties to private array attack types. And then down
here, this->attack types = attack types

Easy. Now I need to make a couple changes down and build character. First thing I
want to do is change the attack type strings into proper attack type objects. I'm
going to use that to do that. I'm going to say attack types equals, and I'm going to
get, I'm going to get a little fancy here. You don't have to do that. I'm going to
use array map and then the cool new short function syntax to say string attack type
equal arrow. This->create attack type, passing in attack type. And then for the
second argument of array map the array that we actually want to map. I'm going to
pass in this->attack types. Now, before I explain that, the way I'm using attack type
now is that we are going to, instead of reading the property, we're going to read an
attack type argument. So let me change that down here. All right, I could have done
this with a four each loop. And if you like four H loops better do it. This basically
says, I want you to loop over all of the attack type strings and for each1 call this
function where we change the attack type string into an attack type object and set
all those attack type objects onto a new attack type variable. So this is now an
array of attack type objects.

Now an easier way to handle this, the rest of this is I can say if there is exactly
one attack type, then I'll say attack type = attack types. And I'll just grab that
one off of there. It means we just have a sword, for example, otherwise we'll say
attack type = new multi attack type. And then we'll pass in the attack types down
here. Instead of calling this->create attack type, we'll just pass that the attack
type variable. So you can see it's get a little bit uglier in here, but that's okay
cause it makes you use in this class very user friendly. All right, let's give us a
test. I'll run our command. Let's be a major archer. Awesome, no error. So I'm going
to assume that that probably worked okay. In game application, we are instant the
character builder manually. But what if the character builder needs access to some
service inside of it, Like maybe it needs the entity manager so it can make some
database queries. Next, I want to make this example a little bit more useful by
seeing how we handle the creation of this character builder object in a real Symfony
app, leveraging the service container. Plus we'll talk about the benefits of the
builder pattern.

