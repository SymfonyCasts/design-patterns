# Design Pattern Types
Hey friends! Thanks for hanging out with me as we go through some fun, geeky, but also *important* topics. We're talking *design patterns*. The idea is simple: The same problems that we face in our code every day has been faced a *million* times before, and often, a way or strategy to solve that problem has already been perfected. These are called "design patterns".

A design pattern is nothing more than a strategy for writing your code when you have a particular problem. If you can start to identify which types of problems are solved by which strategies, you'll walk into situations and immediately know what to do. Learning these design patterns gives you:

A) More tools in your developer toolkit when coding and
B) A better understanding of core libraries like Symfony, which leverages design patterns frequently.

It's pretty cool to look at core code and say

`Oh, I see what you're doing there. That's the
decorator pattern.`

There are tons of design patterns and many of them will likely never be useful to you. They just won't ever apply to problems that you are facing in your real work. These design patterns fall into three basic groups. You don't need to memorize these, but it's a nice way to think about the three types of problems that design pattern solve.

The first type is called "creation patterns", and these are all about helping *instantiate* objects. They include the factory pattern, builder pattern, singleton pattern, and others. The second is called "structural patterns". These help you organize things when you have a bunch of objects and you need to identify relationships between them. One example of a relationship would be a parent-child relationship, but there are *many* others. This one can be a little fuzzy, but we'll see *one* structural pattern in this tutorial - the "decorator pattern". The third and final type of patterns are called "behavioral patterns", which help solve problems with how objects communicate with each other, as well as assigning responsibilities between objects. That's a fancy way of saying that behavioral patterns help you design classes with one specific responsibility that can then work together, instead of putting all of that code into one *giant* class. We'll talk about *two* behavioral patterns - the "strategy pattern" and the "observer pattern".

Now that we've defined some of what we're looking at, it's time to get technical! We're going to use some of these patterns in a real Symfony project to do *real* stuff. We'll only cover a few of them in this tutorial, which are some of my favorites, but if you finish this tutorial and want to see more, let us know!

All right, to get the most out of this tutorial, as always, you should definitely download the course code from this page and code along with me. After you unzip it, you'll find a start directory that has the same code that you see here. Pop open this `README.md` file for the set of details. This one's pretty easy. You just need run:

```terminal
composer install
```

The app we have here is a simple command line RPG where characters battle each other and level up. It's my *favorite* type of game. To run it, run:

```terminal
./bin/console app:game:play
```

Sweet! We have three character types! Let's be a *fighter*. We're fighting *another* fighter. Queue epic battle sounds! And... we won! There was 11 rounds of fighting, 94 damage points dealt, 84 damage points received, and if we want, we can fight *again*. And... woohoo! We are on a *roll*!

This *is* a Symfony app, but it's a very *simple* Symfony app. The first thing we have is this `Command` class. I want you to understand a little bit about how the app is set up right now. This basically just sets up a scenario and prints the results. You tell it which character you want to be and then it starts the battle. But most of the work is done here via the `game` object, which is this `GameApplication.php`. This takes these two `Character` objects and it actually goes through the logic of having them "attack" each other until one of them wins. At the bottom, it also contains the three characters we have, which are represented by this `Character` object. You can pass in different stats for your character, like your `$maxHealth`, the `$baseDamage` that you do, and different `$armor` levels. So this application has three character types down here, and then battles them up up here. That's basically it.

So next, let's get into our first pattern - the "strategy pattern" - where we enable our characters to cast magical spells. That means we need to make this class *a lot* more flexible.
