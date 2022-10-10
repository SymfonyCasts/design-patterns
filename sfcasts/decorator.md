# Decorator Pattern

Coming soon...

One more design pattern to go. And honestly I think we may have saved the best for
last. It's the decorator pattern. This pattern is a structural pattern, so it's about
how you organize and connect related classes. That'll make more sense as we uncover
it. Now the technical definition is the decorator pattern allows you to attach new
behaviors to objects by placing these objects inside special wrapper objects that
contain the behaviors. Hmm, how about this definition? The decorator pattern is like
an intentional man in the middle attack. You replace a class with your custom
implementation, runs some code, then call the true method before we get any deeper
and nerdier. Let's see it in action. So here's the goal. I want to print something
onto the screen whenever a player levels up the logic for when people level up is
actually inside of XP calculator, but instead of changing the code in this class,
we're going to apply the decorator pattern so we can add stuff to this class without
adding code to this class. This is an especially common pattern to use if XB
calculator were a vendor service that we actually could not modify and especially if
the that service XB calculator didn't give us any other way to hook into it, like it
didn't implement the observer or strategy patterns

For the decorator pattern to work. There's just one rule, the class that you want to
decorate, meaning the class you want to extend or modify. So XP calculator in our
case needs to implement an interface. You'll see why in a few minutes. Now if XP
calculator were a vendor package, we just have to hope that they did a good job and
made it implement an interface. But since it's our code, we can add one. So in the
service directory, I'm going to create a new class and actually change it to an
interface. We'll call it XP calculator interface. And then I'll go steal the method
signature for add X p, paste that here, add a use statement and add a semicolon. Easy
enough over an XP calculator, we will implement XP calculator interface. Then finally
open up our XP earned observer. This is the one place in our code we're using the XP
calculator. Change this to use the XP calculator interface type end. Now this staff
was important. Why? Because we are now type hinting the interface. We are going to be
able to swap out the true XP calculator for our own implementation known as the
decorator. So let's create that decorator class in the source service directory,
creating a PhD class and let's call it, how about

Outputting XP calculator? Cuz it's going to be outputting some information to the
screen. It's an XP calculator that outputs things to the screen. Now the key thing
with a decorator class is that eventually the, is that this class is going to be
responsible for calling the real method on the real service. So we actually need to
pass the real service into it. So in other words, we're going to create an pull out
function_under score of construct. And this is going to accept a private read only XP
calculator interface and we'll call it inner calculator. Cause if you think of, Well
now of course our outputting XB calculator also needs to implement the XB calculator
interface so that we can pass it to things like our observer. And this is going to
require us, if I go to "Implement Methods" to have the ad XP method. Now let me add
that missing youse statement there. Perfect.

Now the one main thing that the uh, decorator's always going to do is it's going to
call that inner service. So this error add XP winner, an enemy level. So if something
calls add XP on the outputting XP calculator, we will then call it on the inner XP
calculator. Oh, let me, this will make more sense if I actually say this-> inner
calculator->at xp. So you can see we almost creating like a chain here and that's one
of the benefits of decorators is we're going to have just two XB calculators in our
application, the outputting version and the real version. But technically we could
have multiple decorators. We could have decorators decorating other decorators,
decorating other decorators, as many as we want. And so down here you can see that we
really have the ability to run code before and run code after we call the inner one.
So before we're going to say before level = winner->get level. So we'll kind of store
the level before we add the xp. Then down here we'll say after level = winner->get
level. Then if after level is greater than before level, we know that we just leveled
up.

And so let's print some stuff. I'll say output = new output console output. This is
again, just a kind of cheap way to write console and I'm just going to paste in a
couple lines here that output a nice little congratulations you've leveled up and
what lovely you're on right now. Awesome. Our decorator class is now done, but how do
we hook this up? We basically want to replace any usages of XP calculator in our
system with the new outputting XP calculator. So let's do this manually first without
Symfony fancy container stuff. So what we effectively need to do, there's only one
place in our code that uses XP calculator and it's our XP earned observer. So I'm
going to go into my source kernel.php and temporarily I'm going to comment out this
subscribe magic that we had from earlier. The reason I'm doing this is because I want
to temporarily, manually instantiate my XP earned observer and I will manually
subscribe that onto our game application. We're going to do this inside of source
command, game command. So this is actually what we had earlier before we added that
kernel magic. So we, we could say XP calculator = new XP calculator

And then this->game->subscribe, new XP earned observer and then pass it XP
calculator. So now just manually reg calling subscribe on our game application.
Notice we're not using our new decorator yet, but it should still give us XP when we
level up. So if we run our command and if we win, I didn't want win. Perfect. I won
and awesome. Our XP is increasing, which means that our XP earned observer is doing
its job. So how do we use our decorator? The answer is by sneakily replacing the real
XP calculator with the fake one. Watch this XP calculator = new outputting output
outputting XP calculator, and then pass at the original XP calculator. That's it.
Suddenly, even though it has no idea the XB earned observer is being passed our
decorator service, how cool is that? So let's start over. Try it again and keep
fighting a few times. Remember that new decorator reacted should print a special
message the moment that we level up to the next level. So I'll fight and got it.
You've leveled up to level two. It's working. If you're wondering why we leveled up
before the battle actually started, that may be because of our our game command.

That might be because these icons here are really for decoration. The battles
actually already finished by the time that happens

<affirmative>.

Okay? So we have successfully created a decorator service. Awesome. So how do we kind
of hook this up and replace the core XB calculator with the decorator inside of
Symfony Container? Let's find that out next. Plus we'll do something even cooler.
We're going to completely replace a core Symfony service with our own custom
decorator.

