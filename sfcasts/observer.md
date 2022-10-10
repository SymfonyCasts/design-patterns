# Observer Pattern

Coming soon...

Okay, time for pattern number three, the observer pattern. Technical definition time.
The observer pattern defines a one to many dependency between objects so that when
one object changes state, all of its depends are notified and updated automatically.
Okay, not bad, but let's try this. The observer pattern allows a bunch of different
objects to be notified by a central object when something happens. This is the
classic situation where you write some code that needs to be called when something
else happens. Actually, there are two strategies to solve this. The observer pattern
and the pub sub pattern, we'll talk about both, but first up, the observer pattern.
There are two different types of classes that go into creating this pattern. The
first is called the subject. That's the central thing that will do some work and then
notify other objects before or after it finishes. Those other objects are called are
the second type and they're called observers. It's pretty simple. Each observer tells
the subject that I want, that it wants to be notified. Then later the subject loops
over all of the observers and calls a method on them back in our real app, we're
going to make our game a bit more interesting by introducing levels to each to the
characters. Each time you win a fight, your character will earn some XP or experience
points. And after you've earned enough points, the character will level up, meaning
it's base stats

Like health and damage will increase. Now to write this functionality we put could
put the code right here inside of game application, like after the fight finishes, so
maybe down here and finish fight result, we would do the XP calculation and see if
the user needs to character can level up. But to better organize our code, I want to
put this new logic somewhere else and then use the observer pattern to connect
things. Game application will be the subject and it will notify any observers when a
battle finishes. Another reason the observer pattern might be used is if game
application were actually third party code, like some vendor library and that vendor
library wanted to give us the user of the library some way to run code after a battle
finishes. The first step to this pattern is to create an interface that all the
observers will implement. So for organization awkward and observer directory inside
of there, create a new piece, we class, but I should change it to an interface and
we'll call it Game Observer interface. Inside of it, we can create, we just need one
public method. We can call it anything. How about on fight Finished.

So why do we need this interface? Because in a minute we're going to write code that
loops over all of the observers inside of game application and calls a method on
them. So we need a way to guarantee that each observer has some method like on fight,
finished and actually we can pass on fight, finished whatever arguments we want. In
this case, let's as passed a fight result argument because if you're listening to
this, it'll probably be useful to know the result of the fight and I'll put at a void
return type. All right, step two, we need a way for every observer to subscribe to be
notified on game application. So to do that I'm going to create literally a public
function on inside a game application called subscribe. This is going to accept any
game observer interface and I'll call it observer and we'll make this return void.
I'll fill in the logic there in a second. And the second part, you don't have to do
this, but you can also if you want to add a way to unsubscribe from the changes.
Perfect. Now for the top of the class, let's create a new array property that's going
to hold all of the observers. So private array observers = an empty array and then
help my editor out. I will advertise.

This is an array of game observer interface objects back down in subscribe. It's just
a matter of populating that. So all I add a little check for uniqueness. So I'll say,
if not in array, the observer is not already inside of the observer's list, which
then this error observers let's bracket right square bracket = observer. And then a
similar thing down in unsubscribe, we'll say key = array. Search observer inside of
this error observers. And if the key doesn't equal false, that means we found it in
there. This will onset this->observers key. So just two functions to help us add and
remove things from this->observers. Finally, we're ready to notify these observers.
So we're going to do it right after the fight finishes every time we finish a fight.
This finished fight result is called. So right here I'm actually going to say
this->notify fight results. We don't have to do this, but I'm going to isolate this
logic of notifying the observers down to a new private function down here called
notify. It will accept the fight result argument, return void very simply for each
over this air observers as observer.

And because we know that those are all a game observer interface, we can call on
fight finished and pass fight results. Okay, the subject game application is done.
Now let's implement a concrete observer that will calculate how much XP the winner
should earn and detect if the character should level up. But first we need to add a
couple of fields, a couple of things to the character, a couple of things that the
character class to help with this on top, a private inch level that'll default to one
and a private inch XP that will default to zero. And down here a bit, I'll add a
getter for the level and also convenient function called add X P that will accept the
new X be earned. And I'm actually going to make this return the new XP number. So
inside I'll say this->XP plus = earned XP earned. And I'll return this->x p. And
finally, right after this I'm going to pace one more method in called level up is the
method we'll call when this user levels up and you can see it increases the level and
it increases the max health and the base damage by a little bit. And of course we
could also level up the attack and armor type if we wanted to. Perfect. We're now
ready to implement that observer

Inside the source observer directory, creating a PHP class, let's call it xp, Earned
observer and where all of our observers need to implement the game observer
interface. And I'll go to code generate or command and on the Mac to implement the on
fight finished method. Now the actual guts of Onfi finished, we're going to delegate
the real work to a service called XP Earned Service. If you download the course code,
then you should have a tutorial directory at the XP calculator inside. I'm going to
copy that in source, create a new service directory and paste that inside. So you can
check this out if you want to. But basically what it's doing is it's taking the
character that one, the enemy's enemy's level and it's figuring out how much XB it
should add to the character. And then if they're eligible to level up, it's leveling
up that character. So now over an XP earned observer, we can use that. So create a
constructor so that we can auto wire in a private read only so we can be super
trendy. XP calculator, XP calculator. And then down here, let's get the winner set to
a variable fight result.->get winner,

The loser set to fight result->get loser. And then this->XP calculator->add XP pass
at the winner and loser arrow. Get level. Beautiful. So we now have the subject game
application done. We have the observer done. The next last step is to instant you the
observer and make it subscribe to game application. We're going to do this manually
inside of game command. So source command, game command inside of execute, which is
where we're currently initializing all of the code inside of our application. In a
few minutes we'll see a more Symfony way of connecting all of this. For right now,
we'll say XP Observer = New xp Earned Observer. We'll pass that a new XP calculator
service so it's happy. And then we can say this->game. To use the game property, we
have->subscribe, XP Observer. So we're subscribing the observer before we actually
run our application down here. Okay, we're ready. But just to make it a bit more
obvious, if this is working, head back to character and add one more function here
called Get xp, which will return in to return this->xp cuz this will allow us inside
of game command. If you scroll down a little bit to print results, here we go.

We had a couple new things up here like XP set to player. So whoever we are, that's
who we actually care about. You get XP and we'll do the same thing for final levels.
The level they are after they finish their battle player error. Get level. All right,
let's try this thing. Spin over, run bin console, app game play and let's play as the
fighter. Cause that's one of, it's still one of the toughest characters. And awesome
because we won, we got 30 xp, we're still level one. So let's fight a few more times
here. Oh, we lost. So no XP there. Now we have 60 xp, nine D xp.

Woo.

We leveled up. Final level two, it's working. So what's great about this is that game
application doesn't need to know or care all about the XP and the leveling of logic.
It just notifies its subscribers and they can do whatever they want. All right, next,
let's see how we could wire all of this up using Symfony's Container. Plus we'll talk
about the benefits of this pattern and what parts of Solid it helps with.

