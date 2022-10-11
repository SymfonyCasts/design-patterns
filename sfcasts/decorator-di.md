# Decoration with Symfony's Container

Coming soon...

We just implemented the decorator pattern where we effectively wrapped the original
XP calculator with our outputting XP calculator, then slipped that into the system in
place of the original without anybody else like XP earned observer knowing or caring
that we did it. But to set up the decoration part, I'm instantiating the objects
manually, which is not very realistic. What we want is for XP earned Observer to auto
wire XB calculator interface like normal without us having to do any of this manual
instantiation. But we want it to be past our xp, our output XB calculator decorator
service. So how can we accomplish that with the container? How can we tell the
container that whenever anybody type ins XP calculator interface, I want you to pass
us the decorator. Well, first let's undo our manual code. So in game command and then
kernel, I'll put back the code that attaches the observer to game application. All
right? If we try it now,

It

Fails. Cannot auto wire service XP earned observer? Because the argument XB
calculator references the XB calculator interface, but no such service exists. You
should maybe a alias this interface to one of the existing services and then it lists
our two that implement that. So this makes sense inside of our observer. We're now
type hinting the interface instead of a concrete class. So unless we do a little bit
more work, Symfony doesn't know which XP calculator interface service to pass us. So
the way that we tell it which service to pass for an interface is we create an alias
in config services, do yaml, we'll say app /service /XP calculator interface. And
then to make this an alias to another service, we'll just set it directly to app and
then app service outputting XP calculator. Awesome. So now anywhere in the system
that was auto wiring XP calculator interface will now get our decorator. All right,
so let's try it again. And it still doesn't work. Circular reference detected done.
That makes sense too. Symfony is auto wiring. Our XB calculator outputting XB
calculator into xp Earned observer, but it's also auto wiring output XB calculator
into itself.

Whoops. So we want our outputting XP calculator to be used everywhere in the system
that auto wires XP calculator interface except for this one case to do that back in
services.yaml, we can just manually configure that service app /service /outputting
XP calculator arguments. And then we can say inner calculator. That's the name of our
arguments out of there. And we'll set this to at app /service /XP calculator. So we
override what's going to be passed in just that one case. And now it seems to work.
And if we play a few things and fast forward, yes, you can see there it is.
Congratulations you've leveled up. It did go through our decorator. Okay, so the way
that, let me close a few classes. So this way of wiring up our decorator is not our
final solution, but before we get there, I have an even bigger challenge. Imagine
that there's some core Symfony service and you want to extend its behavior with your
own. How can we do that? Well, we could subclass the core service and reconfigure
things. So maybe Symfony's Container uses our class for that. That might work. But
this is the place where decoration shines. So as a challenge, let's extend the
behavior of Symfony's core event dispatcher service so that whenever an event is
dispatched, we dump a debugging message.

So specifically the idea of the service that we want to decorate is called
event_dispatcher. So as I mentioned, we're going to extend the behavior of that core
service through decoration first. Fortunately, the event dispatcher does implement an
interface over on GitHub for Symfony. I'll look for event dispatcher.php. So this is
the actual core service and you can see implements event dispatch or Interface. Cool.
So let's go create our decorator class. I'm going to create a new decorator
directory.

Then

Inside of that new peach class called How about debug event dispatcher decorator. And
you guys know step one is to implement event dispatcher interface. Now this is a
little tricky cuz there's actually three of them. There's psr, that's the smallest
one. That one that extends this contract one, and then that one extends this one from
Symfony /components. We actually want to use the, Actually I need to change all those
extends to subclass. We actually want to use the, the base one here, the biggest one
to use the one from Symfony /component. The reason is that if our event dispatcher
decorator is going to start being passed around this system in place of the real one,
it needs to implement the strongest interface, the interface that has the most
methods on it,

<affirmative>.

All right, now I'm going to go to go to CO or command N and I'll go and "Implement
Methods" to implement the bunch that's needed. Woo, there we go. And of course the
other out of decoration I almost forgot is mean to add a constructor where that
event, the inner event dispatcher interface is passed in. So let me make that a
property private read only. Perfect. So in all these methods here, what we need to do
now is just call them. So this is actually pretty boring. It's like this->event,
dispatcher,-> ad listener, event name, listener and priority. And one other thing you
need to worry about is whether or not the parent method returns of value. That's not
always obvious. As we get more return types in Symfony, that is more obvious in this
case there is, you don't need to return in this method. But there are methods down
here that do have return values like get listeners. Now I'm not going to bore you
with just doing that same thing about eight different times. So I'm going to co, I'm
going to delete all these and I'm going to paste in the finished version of all those
methods. You can copy that from the code block on this page. Literally just calling
the all the, the listeners, the inner event dispatch at the same methods, with the
same arguments inside of all of them.

So this point, congratulations, we've created a decorator for our event dispatcher
that doesn't do anything extra yet. All right, so now let's add our behavior. So
right before the dispatch method is called, we're going to dump some information
about the event. So I'm going to kind paste these two dumps here. And then right here
I'll say dispatching event. And then we can say event call. Call in class

There

Simple. Perfect. So now the million dollar question. There are many places in Symfony
that rely on the service whose ID is event underscored, depa dispatcher. So how can
we replace that service with our own service but then get the original service passed
into us? The answer is Symfony has a feature built exactly for this. You're going to
love this. Go above this class and add a pH v eight attribute to called as decorator
and then pass it the name of the service that we want to decorate, event_dispatcher.
That's it. This says, Hey Symfony, please make me the real event_APA dispatcher
service, but make the original event dispatcher service available as to be auto wired
by as an argument to me. Try it, run our app and it works. Look at, you can see the
event being dumped out right here and another event that's our custom event. And if I
exit another event at the bottom, it works. We just replace the core event dispatcher
service with our own by creating just a single class that's bananas. So could we have
used this as D decorator trick earlier for our own XB calculator decoration
situation? Yep. Here's how and config services.yaml remove the manual arguments and
change the interface to actually point to the real service we want. So XP calculator.
So basically in our service config, we get to set things up kind of the normal way
without thinking or worrying about decoration. This is how we would want our
application to work if there were no decorators.

So if we did try our app right now, it would work. But we are not using our
decorator, our outputting XP calculator decorator service. But now we go into
outputting XP calculator, we can add as decorator and then pass this XP calculator.
Oops, out calculator interface. Call on call class. Cause that's actually the idea of
the service we want to decorate. And that's it. If you try to code now no errors. And
actually an even faster way to prove this is working is I'm going to run debug
container, XB calculator interface-dash show-argument. So I'm going to get some
information about what the XB calculator interface is, UH, service is. So when I run
this, check this out, it says that this is an alias for the service outputting XB
calculator interface. So anybody that is auto wiring, this is actually getting the
outputting XB calculator service. And if you look down here at the arguments, the
first argument past to xp outputting XB calculator is the real XP calculator that
That's awesome. All right. Decorator pattern done. What a cool pattern. One property
of the decorate PA decorator pattern that we didn't talk about was that you can
decorate a service as many times as you'd like. Yep. If we created another

Class that implemented XP calculator interface and gave it this as decorator
attribute, there would now be two services decorating it. Which service would be on
the outside? Well, if you care enough, you can set a priority on these services to
control that. And where do we see decoration in the wild? The answer to that is sort
of all over. In API platform, it's very common to use decoration to extend core
behaviors like the context builder or parts of the serializer in Symfony itself,
Symfony uses decoration pretty commonly to add debug features while you're in
developing mode only. For example, we know that this event dispatcher is the real
service that would be used in the prod environment, but in the dev environment. I'll
hit t to search for a class search for a traceable event dispatcher in the dev
environment, assuming that you have some debugging tools installed, this is the
actual service that represents the event dispatcher. It decorates the real one watch.
I can even prove this. Head back to your terminal and run bin console debug container
event dispatcher event_dispatcher-show-arguments and scroll to the top. Oops on

And scroll the top. Check this out. The event_dispatcher service is actually an alias
for another service called debug.event dispatcher. Guess what this is? This is the
traceable event dispatcher. So the traceable event dispatcher is actually on the
outside and if you scroll down to its arguments, the next one that's inside of it is
our debug event dispatcher. So you have the outer traceable event dispatcher, our
debug event dispatcher inside of it, and then the real event dispatcher inside of
that. Pretty cool. So what problems does the decorator pattern solve? I think this
one is pretty obvious. It allows us to extend the behavior of an existing class. For
example, XB calculator. Even if that class doesn't contain any other extension
points, this means we can use it to override vendor services when all else fails. The
only downside to the decorator pattern is that we can only run code before or after
the core method. And the service we want to decorate must implement an interface.
Okay, team, we are done. Yes, there are many more patterns out there in the wild.
This was just a collection of some of our favorites. There's one or several that we
skip that you really want to hear about. Let us know until then. See if you can spot
these patterns in the wild and figure out where you can apply them to clean up your
own code and impress your friends. All right friends. See you next time.

