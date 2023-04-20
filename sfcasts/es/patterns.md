# Patrones de diseño y sus tipos

¡Hola amigos! Gracias por pasar el rato y concederme el privilegio de guiarnos a través de algunas cosas divertidas y frikis, pero también útiles. Estamos hablando de patrones de diseño. La idea es sencilla: Los mismos problemas a los que nos enfrentamos en nuestro código cada día se han enfrentado un millón de veces antes. Y a menudo, ya se ha perfeccionado una forma o "estrategia" para resolver ese problema. Esto se llama "patrones de diseño".

## ¿Por qué debería importarnos?

Un patrón de diseño no es más que una "estrategia" para escribir código cuando te encuentras con un problema concreto. Si puedes empezar a identificar qué tipos de problemas se resuelven con qué estrategias, entrarás en situaciones y sabrás inmediatamente qué hacer. Aprender patrones de diseño te proporciona

A) Más herramientas en tu caja de herramientas de desarrollador al codificar y B) Una mejor comprensión de las bibliotecas básicas como Symfony, que aprovecha mucho los patrones de diseño.

También te hará más divertido en las fiestas... suponiendo que los únicos asistentes a la fiesta sean programadores... porque podrás decir inteligentemente cosas como

> Sí, me he dado cuenta de que has refactorizado para utilizar el patrón decorador: una gran idea
> para extender esa clase sin violar el principio de responsabilidad única.

Caramba, vamos a ser súper populares.

## Tipos de patrones de diseño

Vale, hay montones de patrones de diseño. Aunque... es probable que sólo un pequeño número nos resulte útil en el mundo real: simplemente no nos enfrentaremos nunca a los problemas que resuelven los demás. Estos numerosos patrones de diseño se dividen en tres grupos básicos. No es necesario que los memorices... es sólo una buena manera de pensar en los tres tipos de problemas que resuelven los patrones de diseño.

El primer tipo se denomina "patrones de creación", y consisten en ayudar a instanciar objetos. Incluyen el patrón de fábrica, el patrón constructor, el patrón singleton y otros.

El segundo tipo se llama "patrones estructurales". Te ayudan a organizar las cosas cuando tienes un montón de objetos y necesitas identificar las relaciones entre ellos. Un ejemplo de relación sería la relación padre-hijo, pero hay muchas otras. Sí, lo sé: esto puede ser un poco confuso. Pero veremos un patrón estructural en este tutorial: el "patrón decorador".

El tercer y último tipo de patrones se denomina "patrones de comportamiento", que ayudan a resolver problemas sobre cómo se comunican los objetos entre sí, así como a asignar responsabilidades entre los objetos. Es una forma elegante de decir que los patrones de comportamiento te ayudan a diseñar clases con responsabilidades específicas que luego pueden trabajar juntas... en lugar de poner todo ese código en una clase gigante. Hablaremos de dos patrones de comportamiento: el "patrón de estrategia" y el "patrón de observador".

## Prepara el proyecto

Ahora que hemos definido algo de lo que vamos a ver, ¡es hora de ponernos técnicos! Vamos a utilizar estos patrones en un proyecto real de Symfony para hacer cosas reales. Sólo cubriremos algunos patrones en este tutorial - algunos de mis favoritos - pero si terminas y quieres ver más, ¡háznoslo saber!

Muy bien, para ser el mejor diseñador de patrones que puedas ser, definitivamente deberías descargar el código del curso desde esta página y codificar junto a mí. Después de descomprimirlo, encontrarás un directorio `start/` que tiene el mismo código que ves aquí. Abre este archivo `README.md` para ver todos los detalles de la configuración. Sin embargo, esto es bastante fácil: sólo tienes que ejecutar:

```terminal
composer install
```

Nuestra aplicación es un simple juego de rol de línea de comandos en el que los personajes luchan entre sí y suben de nivel. Los juegos de rol son mi tipo de juego favorito: ¡[Shining Force](https://en.wikipedia.org/wiki/Shining_Force) para ganar!

Para jugar, ejecuta:

```terminal
./bin/console app:game:play
```

¡Genial! ¡Tenemos tres tipos de personajes! Seamos un luchador. Vamos a luchar contra otro luchador. ¡Poner en cola los sonidos de la batalla épica! Y... ¡ganamos! Hubo 11 rondas de lucha, 94 puntos de daño repartidos, 84 puntos de daño recibidos y ¡¡¡gloria para todos!!! También podemos volver a luchar. Y... ¡woohoo! ¡Estamos en racha!

Esta es una aplicación Symfony, pero una aplicación Symfony muy sencilla. Tiene una clase de comando que configura las cosas e imprime los resultados. Le dices qué personaje quieres ser y comienza la batalla.

[[[ code('325a344686') ]]]

Pero la mayor parte del trabajo se realiza a través de la propiedad `game`, que es esta clase`GameApplication`. Esta toma estos dos objetos `Character` y pasa por la lógica de hacer que se "ataquen" mutuamente hasta que uno de ellos gane. En la parte inferior, también contiene los tres tipos de personajes, que están representados por esta clase`Character`. Puedes pasar diferentes estadísticas para tu personaje, como`$maxHealth`, el `$baseDamage` que haces, y diferentes niveles de `$armor`.

[[[ code('f677da3f6f') ]]]

Así que `GameApplication` define los tres tipos de personaje aquí abajo... y luego los combate arriba. ¡Eso es básicamente todo!

[[[ code('fcdec1f676') ]]]

A continuación: vamos a sumergirnos en nuestro primer patrón, el "patrón de estrategia", en el que permitimos que algunos personajes lancen hechizos mágicos. Para hacerlo posible, vamos a tener que hacer que la clase `Character` sea mucho más flexible.
