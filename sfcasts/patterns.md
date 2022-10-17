# Design Patterns & Their Types

Hey friends! Thanks for hanging out and giving me the *privilege* to guide us
through some fun, geeky, but also *useful* stuff. We're talking *design patterns*.
The idea is simple: The same problems that *we* face in our code every day have been
faced a *million* times before. And often, a way or "strategy" to solve that problem
has already been perfected. These are called "design patterns".

## Why Should we Care?

A design pattern is nothing more than a "strategy" for writing code when you encounter
a particular problem. If you can start to *identify* which types of problems are
solved by which strategies, you'll walk into situations and immediately know what
to do. Learning design patterns gives you:

A) More tools in your developer toolkit when coding and
B) A better understanding of core libraries like Symfony, which leverages design
patterns a *lot*.

It'll also make you *way* more fun at parties... assuming the only people at the
party are programmers... because you'll be able to smartly say things like:

> Yea, I noticed that you refactored to use the decorator pattern - great idea
> for extending that class without violating the single responsibility principle.

Dang, we're going to be *super* popular.

## Design Pattern Types

Ok, so there are *tons* of design patterns. Though... only a small number are likely
to be useful to us in the real-world: we just won't ever face the problems that the
others solve. These many design patterns fall into three basic groups. You don't
need to memorize these... it's just a nice way to think about the three types of
*problems* that design patterns solve.

The first type is called "creational patterns", and these are all about helping
*instantiate* objects. They include the factory pattern, builder pattern, singleton
pattern, and others.

The second type is called "structural patterns". These help you organize things when
you have a bunch of objects and you need to identify *relationships* between them.
One example of a relationship would be a parent-child relationship, but there are
*many* others. Yea, I know: this one can be a little fuzzy. But we *will* see one
structural pattern in this tutorial: the "decorator pattern".

The third and final type of patterns is called "behavioral patterns", which help
solve problems with how objects *communicate* with each other, as well as assigning
responsibilities between objects. That's a fancy way of saying that behavioral
patterns help you design classes with specific responsibilities that can then work
together... instead of putting all of that code into one *giant* class. We'll
talk about *two* behavioral patterns: the "strategy pattern" and the "observer pattern".

## Get that Project Set up!

Now that we've defined some of what we'll be looking at, it's time to get technical!
We're going to use these patterns in a *real* Symfony project to do *real* stuff.
We'll only cover a *few* patterns in this tutorial - some of my favorites - but if
you finish and want to see more, let us know!

All right, to be the best design-pattern-er that you can be, you should definitely
download the course code from this page and code along with me. After you unzip it,
you'll find a `start/` directory that has the same code that you see here. Pop open
this `README.md` file for all the setup details. Though, this one's pretty easy: you
just need run:

```terminal
composer install
```

Our app is a simple command-line role-playing game where characters battle each other
and level up. RPG's are my *favorite* type of game - [Shining Force](https://en.wikipedia.org/wiki/Shining_Force)
for the win!

To play, run:

```terminal
./bin/console app:game:play
```

Sweet! We have three character types! Let's be a *fighter*. We're battling *another*
fighter. Queue epic battle sounds! And... we won! There was 11 rounds of fighting,
94 damage points dealt, 84 damage points received and glory for all!!! We can
also battle *again*. And... woohoo! We're on a *roll*!

This *is* a Symfony app, but a very *simple* Symfony app. It has a command class
that sets things up and prints the results. You tell it which character you want
to be and it starts the battle.

But most of the work is done via the `game` property, which is this
`GameApplication` class. This takes these two `Character` objects and it goes through
the logic of having them "attack" each other until one of them wins. At the bottom,
it also contains the three character types, which are represented by this
`Character` class. You can pass in different stats for your character, like
`$maxHealth`, the `$baseDamage` that you do, and different `$armor` levels.

So `GameApplication` defines the three character types down here... then battles
them up above. That's basically it!

Next: let's dive into our first pattern - the "strategy pattern" - where we allow
some characters to cast magical spells. To make that possible, we're going to need
to make the `Character` class *a lot* more flexible.
