# Design Pattern Types

Yo friends, thanks for showing up so that we can go through some super geeky, but
important and fun topics. We're talking design patterns. The idea is simple. The same
problems that we face in our code every day had been faced a million times before,
and often a way or strategy to solve that problem has already been perfected. These
are called design patterns. A design pattern is nothing more than a strategy for
writing your code. What do you have a particular problem? If you can start to
identify which types of problems are solved by which strategies, you'll walk into
situations and immediately know what to do. Learning these design patterns gives you
a more tools in your toolkit when coding and B, a better understanding of core
libraries like Symfony, which leverage leverage design patterns a lot. It's pretty
cool to look at core code and say, Oh, I see what you're doing there. That's the
decorator pattern. Okay, so there are tons of design patterns and probably many of
them will never be useful to you. They just won't ever apply to problems that you are
facing in your real work.

But these many design patterns fall into just three basic groups, though you don't
need to memorize these, it's just a nice way to think about the three different types
of problems that design pattern solve. The first type are called creation patterns,
and these are all about helping instantiate objects. They include the factory
pattern, builder pattern, singleton pattern, and others. The second are called
structural patterns. These are about helping you organize things when you have many
objects and you need to identify relationships between them. One example of a
relationship would be a parent child relationship, but there are many others. I know
this one is a bit fuzzier, but we'll see one structural pattern in this tutorial. The
decorator pattern in the third and final type of patterns are called behavioral
patterns, which help solve problems with how objects communicate with each other, and
also assigning responsibilities between objects, which is a fancy way of saying that
behavioral patterns help you design classes with1 specific responsibility that can
then work together.

Instead of putting all that code in one giant class. We'll talk about two behavioral
patterns, the strategy pattern and the observer pattern. Okay, enough defining
things. We are going to get technical, but we are also going to use these patterns in
a real Symfony project to do real stuff. We'll only cover a few patterns in this
tutorial, some of my favorites, but if you want to see more after, let us know. All
right. To get the most out of this tutorial, as always, you should definitely
download the code and the code along with me. Download the course code from this
page. After you un zip it, you'll find a start directory that has the same code that
you see here. Pop open this readme.md file. For the set of details though, this one's
pretty easy. You just gotta run, composer, install. The app we have here is a simple
command line R PPG, where characters battle each other and level up. It's like my
favorite type of game to run it. Run bin console app, co game,:play. Okay, sweet. We
have three character types. Let's be a fighter. We're fighting another fighter Q Epic
Battle sand sounds, and we won 11 rounds of fighting 94 damage doubt, 84 damage
received. And if we want, we can fight again and oh, we are on a roll.

All right, so this is a Symfony app, but it's a very simple Symfony app. It's not
even the first thing we have is this command class. I want you to understand a little
bit about how the game, how the app is set up right now. This basically just sets
things up and prints the results. It figures out which character you want to be and
then it actually starts the battle. Um, but most of the work is actually done, is
done here on this game object, which is this game application. This takes these two
character objects and it actually does the logic of having them attack each other
until somebody wins. It also at the bottom contains the three characters we have,
which are represented by this character object and that character object. You can
pass in different max health, different base damage that you do and different armor
levels. So game application with care three characters here, and that battles them up
up here, and that's basically it. So next, let's get into our first pattern, which is
going to be this strategy pattern where we make our characters able to cast magical
spells, which will require making this class a lot more flexible.
