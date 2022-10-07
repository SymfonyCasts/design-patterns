# Builder in Symfony & with a Factory

Coming soon...

What if in order to instant the Character Objects character builder needed to, for
example, make a query to the database? Well, when we need to make a query to the
database, what we would normally do is give our class a instructor and then auto wire
the service that we need. But Character Builder isn't a service. I mean, you could
use it like a service, but a service is typically a class where you only ever need
one instance of that class. But in game application, we're creating one character
builder per character. If we did try to auto wire character builder into a game
application that would work, Symfony would auto wire the entity manager into
Character Builder and then it would pass us the character builder object with the
entity manager inside. The problem is it would mean that we only had one character
builder and we ne and we actually need four character builders in order to create our
four character objects. This is why commonly builder objects are partnered with a
builder factory. So let me actually undo all of those changes that I just made to
game application and also character builder. All right, over here in the builder
directory,

We're going to create a new class called Character Builder Factory. By the way, there
is a pattern called the factory pattern, which we're not talking specifically about
in this tutorial, but a factory is just a class whose job is to create another class.
It like the builder pattern is a creation pattern. So instead of here, I'm going to
create a new method called How About Create Builder, and it will return a character
builder and literally inside we'll just return new character builder. Now, this
character builder is a service. Even if we need five character builders in our
application, we only need one character builder factory. We can just call this method
on it five times. That means that over in game application, we can create a
construction in Otta wire character builder, factory character builder factory. And
let me put private in front of that so that it is a property also. And then down
inside of our Create character builder, instead of creating this by hand, we'll rely
on the factory return. This-> character builder, factory->create builder. The nice
thing about this factory, and this is really a benefit of the factory pattern in
general, is that we have centralized the instantiation of this object.

How does that help us in this situation? Well, remember the problem I was imagining
is what if our character builder needed a service like the entity manager with our
current setup, their new setup. We can make that happen. I don't actually have
doctrine installed in this project. So instead of the entity manager, let's auto wire
logger interface logger, and I'll add private in front of that to turn that into a
property and then down and build character. Just to test that this is working, let's
use that, this->logger->info, creating a character. And I'll pass a second argument,
which is some context we could say like Max Health set to this era, Max health base
damage. And if you want to, you could add more stats below that, but that's enough
for us. So now that character builder requires a logger, we can very easily handle
that in Character Builder factory. This is a service. So auto wiring, a logger
interface will work here

And then we can just pass that manually into logger. So we're seeing a little bit of
the benefit of a factory pattern since we already centralized the creation of the
character builder. Anywhere that we use this factory like game application doesn't
need to change at all. So even though we added a new constructor argument to a
character builder, our game application doesn't need to change. It was offloading all
of that work to the character builder factory. All right, see if this is work, then
we can run bin console, app game, play with little-vv. So we see log messages while
we play this

And

Got it. Look it info, creating a character. We can't see the other stats right here
on this screen, but they would be shown in a log file. Awesome. So that is the
builder pattern. What problems does it solve? Simple. You have an object that's
difficult to instantiate, so you add a builder class to make life easier. It also
helps with these single responsibility principle. It's one of the strategies that
helps abstract creation logic of a class away from the class that will work with that
object. So previously in game application, we both had the complexity of creating the
objects and then using them. Now we still have code here to use the builder, but most
of the complexity is off in that builder class. Oh, and frequently when you study
this

Pattern's done together,

He will tell you that the builder, so like Character builder should implement a new
interface like implements character builder interface, which would have methods I
like set max health, set based damage, et cetera. This is optional. When would you
need it? Well, like all interfaces, it's useful if you want to be able to swap how
your characters are built at run time. For example, imagine we created a second
builder that implemented character builder interface called Double Hit Points
character built double max Health Character Builder, which creates the, which creates
the character object, but in a slightly different way that maybe doubles the Max
Health.

Nobody,

If both of those builders implemented this Character builder interface, then inside
of our Character builder factory, which would now return a character builder
interface, we could actually read some config here and figure out which character
builder we want to use. So it really has a lot to do with the builder pattern itself,
that interface and more to do with just making your code, um, more flexible. So let
me undo that fake code inside of Character Builder Factory and since I've character
builder, I'll remove that Make believe interface. Oh, and where do we see the builder
pattern in the wild? This one is pretty easy to spot. Since Method Chaining is such a
common feature of Query Builders. Of Builders, the first one that comes to mind is
Doctrines Query Builder, which is no surprise based on its name. It allows us to
configure a query with a bunch of nice methods before finally calling Get Query to
actually create the query object. It also leverages that factory pattern because the,
you call Create Query Builder where the base EntityRepository is responsible for
instantiating the Query builder. Uh, another example is Symfony's Form Builder. In
that case, we don't call the build form method, but Symfony eventually does once
we're done configuring it. All right, next up, let's talk the observer pattern.
