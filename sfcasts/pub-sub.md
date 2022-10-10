# Publish-Subscriber (PubSub)

Coming soon...

The next pattern pattern I want to talk about maybe isn't its own pattern in reality
it's more of a variation on the observer pattern. It's called pub sub or publish
subscribe. The key difference be between observer and pub sub is simply who handles
notifying the observers with the observer pattern. It's the

Subject, it's the thing in our case game application that does the work. It does the
work and it's responsible for notifying the observers with pubsub. There's a third
object called a publisher, or what I'm going to call it is an event dispatcher, a
name that's probably more familiar to a lot of us with Pubsub. The observers also
call listeners tell the dispatcher which events it wants to listen to. Then the
subject, whatever is doing the work simply tells the dispatcher to dispatch this
event. The dispatcher is then responsible for actually calling the methods on those
objects. You could argue that Pubsub better follows the single responsibility
pattern, battling characters and registering and then calling the observers. Ours are
kind of two separate responsibilities that we've jammed inside of a game application.
So let's use the pubs up pattern to be to add new functionality to our app. I want a
way to run code before a battle starts. So step one is we are going to create an
event class. This will be the object that is passed as an argument to all of your
listeners. It's basically identical to the fight result that we pass to our
observers. But with the pub sub pattern, it's customary to create a an event class
just for the event system. So inside of a inside of source I'm going to create a new
event directory.

Inside of there a new, new peach with classic could be called anything. We're going
to call it fight Starting event. This class doesn't need to look like anything. It
doesn't need to extend anything though. We will talk more about it in a minute. All
right, step two is we're going to dispatch this event from inside of Game App
location. Now instead of writing our own event dispatcher, we are going to use
Symfony Event Dispatcher. So let me break the instructor multiple lines. Let's add a
new private event dispatcher interface event dispatcher so that an auto wire Symfony
event dispatcher here. Then down in play right at the very beginning we'll say this
area event dispatcher,->dispatch, and then create a new fight starting event. That's
it. That's enough for the dispatcher to notify all of the code that is listening to
the Fight Starting event right now. That's nobody.

So finally we are going to register a listener for this event to start, let's do this
inside of a game command, the place where we're kind of setting up and initializing
our app. We will see how to do all of this properly with Symfony's container in a
minute, but I want to keep it simple to start. So same thing here. We need to get
access to that same event dispatcher. So we are going to auto wire it, Private read
only event dispatch or interface. Yes, I am being a little inconsistent with when I
use Read only and not technically. I could use Read only on all of the construct
arguments. It's not really important. Now down here kind of before, anywhere before
our application actually starts, I'm going to say this-> the event dispatcher->at

Shoot

Arrow. And notice the only method on this is actually called dispatch. This is a
little mistake I made. Let me back up here a second. In game application when I auto
wired event dispatcher interface, I actually got the one from PSR event dispatcher.
Event dispatcher interface. Um, which is, which is great and it's said game command.
I actually did the same thing. I got the PSR event dispatcher. But if you want the
ability to actually attach listeners at run time instead of the one from psr, get the
one from Symfony Component Event dispatcher. In reality, even though we're using two
different type hints in these classes, this is going to pass as the same event
dispatcher object. That event dispatcher object has a method on it called a listener.
So even if I'd used the old interface, this method would have existed. It would've
just looked funny inside of my inside of my editor. Um, now the first argument of
this is the name of the event. This is going to match the class name that we're
dispatching. So we can actually say fight starting event call on class. And then to
keep it really simple, I'm going to be lazy here, just pass an inline function.

Sweet and I'm going to u use the IO function so I can get that inside of here. We'll
say IO note fight is starting and that's it. We are dispatching defense inside of
game application and we've already registered the listener here, so it should call it
let's try it. Pin console, app game play, choose our character and got it note fight
is starting. If a fight, again we get the same thing again, awesome, but it would be
cooler if we had a little bit more information inside of our listener, like who won
the fu the battle. That's the job of this event class. It can really carry whatever
data we want. So for example, let's create a public function unders corner score
construct. And for simplicity I going to create two public properties called Player
ai. Awesome and then a game application. We did actually pass those in. So pass
player and AI and then over in our listener, now our function's going to get past a
fight starting event. Object. It always was, it just wasn't very useful before. So
now you can say fight is starting against and then say Event-> AI->get nickname

Super nice, let's give it a try. I'll run the command again. And Sweet Fight is
starting against AI age. The only thing I did is miss my space there to make it look
nicer. So as I mentioned, you can really put whatever data you want on this fight
starting event. Heck, you could create a public should battle property if you wanted
to. And then your listener if you wanted to, you could say Event->should battle
false. And in game application you could actually use that. You could set this event
to a new event, object, dispatch it, and if they shouldn't battle, you could just
return. Or I guess you guys return fight results and maybe throw an exception.
Whatever you see the point, you can actually even kind of send signals back about
what you want to do. So let me actually undo all of that inside of game application
and Fight starting event and also game command. All right? Now as easy as this in
Line Listener is, it's more common to create a separate class for your listener. You
can either create a listener class, which is basically a class that has this code
here as a public function

Or you can create a class called a subscriber. Both are completely valid ways to use
the pub sub pattern. The only difference is how you register a listener versus a
subscriber, which is a pretty minor difference and you'll see it in a minute. So
let's refactor this to a subscriber in the event directory operated PHP class called
How about Output Fight Starting subscriber because this subscriber's going to output
that a battle is beginning. Now, events don't need to event listeners don't need to
extend any base class or implement any interface. But event subscribers do they need
to implement the event subscriber interface. Then I'll go Code Degenerate or Command
N on a Mac. You can go to "Implement Methods" and say Get Subscribed events. So
within Event subscriber, you actually say right inside this class with which events
you subscribe to. So here we'll say fight starting event, Call, call class, and then
say, set that to you on Fight Start. So this says, when that event happens, I want
you to call an on Fight Start method in this class. So we'll create that public
function on Fight Start. This will get the Fight Starting event object of course.

And then for the guts of this, I'm going to go over to game command and steal our io.
And the only thing is the IO is actually kind of a hard thing to pass from console
commands into other parts of your code. I'm going to ignore that complexity here and
just create a new IO by saying New Symfony style, new array input, passing an empty
array and new console outputs. I'm creating an object that's just like the normal IO
object so I can kind of cheat inside of here. Finally, now that we have a subscriber
back in game Command, we're going to hook that up. So instead of add event listener,
add subscriber, and I'll say, New output fight, starting a subscriber, awesome
testing time, moment of truth, I'll exit and choose my guy. And wow, it's working so
well. It's outputting twice. Why? This is thanks to once again auto configuration. We
are using auto configuration inside of our application. So whenever we create a class
that implements event subscriber interface, Symfony containers, automatically taking
that class, taking that subscriber and registering it on the event dispatcher. In
other words, Symfony internally is already calling this line right here. So that kind
of answers the question of how we use the events. The pops up pattern in Symfony,

Delete that line inside of Game Command. All you need to do is create an event
subscriber like we've done here and Symfony will automatically register it. And then
to dispatch an event, you'll create a new event class. And anywhere in your code you
can dispatch that event. So you can see how easy it is to create the event dispatcher
and dispatch all kinds of events all over your application. Now the benefits of the
pub up now if we try this over here, I'll exit I battle again, it only outputs once.
Now the benefits of Pubsub are really the same as the observer, but in practice the
pump up is a bit more common probably because Symfony is such a great event
dispatcher. So honestly, half of the work is already done for you. Next, let's dive
into our final pattern and one of my favorite and the most powerful and Symfony, the
decorator pattern.

