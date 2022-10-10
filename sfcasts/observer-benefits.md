# Observer inside Symfony + Benefits

Coming soon...

We've implemented the Observer Pattern Game application is our subject, which
notifies all of our observers and we have one Observer XP Earned Observer. But the
way we connected them inside of Game Command up here by manually and instant it in
the Observer and the XP Calculator service and then manually calling this a
Game->subscribe isn't very Symfony like. Both the Observer and XB calculator are
services. So we would normally auto wire them from the container and we are auto
wiring game application. And even that isn't quite right. By the time we Symfony
gives us this game application, <affirmative> Symfony should ha Symfony's Container
should have already hooked up all of its observers so that it's absolutely ready to
go immediately. So how can we do that? First, we'll do it the simple way. Remove all
of this manual code that we don't want inside of the game command. And what we're
going to do is basically just configure that same thing, but inside of Services.yaml.
So open config services.yaml, and at the bottom we are going to modify the service
app /game application. And we're not going to pass any arguments to it. We're going
to add a calls to it. So this says, after you instantiate the game application, call
the subscribe method on it

And pass as an argument

The app observer, the XP earned observer service. So when we auto wire game
application, Symfony will then go grab the XP Earned Observer service and the XP
Earned Observer service will get the XP calculator service auto wired into it. So
it's all a lot of normal auto wiring. The only difference is that Symfony is going to
call the subscribe methadone game application before it gives us the game application
service inside of Game Command. So in other words, this should work. Let's give it a
try. Bend console app game play, no errors and oh, we lost bad luck. Let's try again.
We won and we got 30 X p. It's working. But this means that

Every time we add a new observer, we'll need to come to Services.YAML and wire it
manually. How undignified could we automatically register, subscribe all services
that implement Game Observer interface. Why? What an excellent idea in doing this is
two steps, Open source /nel php. This isn't a file that we work with much, but we're
about to do some deeper things with the container and this is the right place. The
first thing I want to do, I'll go is go to CO or I'll do commando and go to override
methods. We're going to override one called Build Perfect. This parent method is
empty, so we don't need to call the Parent method. And instead of here we're going to
say Container-> register for auto configuration, pass it Game Observer interface,
call on class and then say add tag. And I'm going to invent a new tag here called
game.observer. So this is probably not something you see very often or ever, but this
is something that the Symfony Core and third party bundles do all the time. This
says, any service that implements Game Observer interface automatically give it to
this game.observer tag if that service is auto configurable. Now I just made up this
tag name right here and right now it doesn't do anything. Giving a tag to a service
is like tagging a blog post, but we should be able to see it. So if we spin over and
run Bin Console, debug container,

And search for xp Earned Observer, Beautiful it found it. Oh, all right. And then
do-dash tAJAX on that. Oops. Oh, alright. Right. I don't need do that. And beautiful
at founder service checks out tAJAX. game.observer. Alright, so now that our service
has a tag, we're going to write a little more code that automatically calls the
subscribe method on game application for every service that has that tag. So this is
also going to go in ker, but we're going to override a different method. In this case
actually, we're going to implement what's called a compiler pass. So add a new
interface to your kernel called Compiler Pass. And then down here I'll go to code
Generate "Implement Methods" process. So a compiler pass is a little bit more
advanced. It's a piece of code that runs at the very, very end of your container
being built and you can do whatever kind of crazy things that you want to do inside
of there. So check this out. The first thing we're going to do is we're going to say
definition = container arrow. Find definition game application class. So this is not
going to return us to the game application service, but a game application service
definition, an object that knows everything about how to instant a game application
like it's class and it's constructive arguments and any calls it might have on it.
Next I'm going to say tagged

Observers = container arrow. Find tag service IDs and then say game.observer. This is
going to return an array of all the services that have this game.observer tag, and
then we can look over them for each tAJAX observers as ID aero tAJAX. So this is
going to be the service ID that has that tag. And sometimes a tag can be, uh, service
can be tagged multiple times. So this will actually be multiple tAJAX inside of here.
We don't need to worry about that. All we're going to do is say definition. So we're
getting the game application definition-> add method call. It's basically the PHP
version of the calls right here. And we're going to say, Hey Symfony, I want you to
call the subscribe method on the game definition. And for arguments, I want you to
pass it, say new reference, the one from Dependency injection. And then id, This is a
really fancy way of saying that we want the game of the subscribe method to be called
on game application and for it to pass us the service that had the Game Observer tag.
So now that we have this, this is basically the same thing that we have in
Services.yaml, just more dynamic so we can remove all this code and services.yaml

And now let's try our game again, app game play, no errors. And yes, it's still
works. And if we needed to add another observer later, we can just create a class,
make it implement Game Observer interface and done it will automatically be
subscribed to the game application. So that is the observer pattern. It can look
different than this with different methods or sometimes these observers are injected
through the constructor, but the idea is always the same. A central object loops over
and calls a method on a collection of other objects when something happens. Where do
we see this in the wild? Well, it's at least kind of in a bunch of places though. The
next pattern we'll talk about pub sub is a lot more common. But here's one example
over on somebody's getup page, I'm going to hit T and search for a class called
Locale. Switcher. If y, if you in your application need to do something at each time
the locale switches, you can register your code with the locale switcher and it will
call, in this case, the observers are actually passed through the constructor. And
then you can see down here after the local is set, it loops over all of those and it
cuts the methods set locale on it. So this, this classes are subject and these are
our observers.

How do you register an observer? No surprise. It's by creating a class that
implements locale Aware interface thanks to auto wiring. Symfony will automatically
tag your service with kernel.locale aware, so it uses the same mechanism for hooking
all this up. As we just saw. Now, the benefits of the observer pattern are actually
best described by looking at these solid principles. This pattern helps the single
responsibility pattern because you can encapsulate or isolate code in the smaller
classes. Instead of putting everything into game application. Like all of our XP
logic right here,

We

Were able to isolate things over into XP earned observer and keep both classes more
focused.

This pattern also helps with the open closed principle because we can now extend the
behavior of game application without modifying its code by registering and observer.
Finally, though, this principle is always trickier for me. The observer pattern
follows the dependency inversion principle where the high level class game
application accepts in interface face game observer interface. And that interface is
designed for the purpose of how game app will use it like by calling the method on
Fight Finished. It's not designed based on how the observers will use it. Like if we
had called the interface, uh, XP, change your interface and the method time to change
the xp, that would be a violation of the D I P principle. If that's all a little
fuzzy to you, it's not a big deal, but you can watch our SAL tutorial if you want to
know more. All right, next, let's quickly turn to the brother pattern of Observer Pub
Sub.
