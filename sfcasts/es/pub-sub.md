# Publicar-Suscribir (PubSub)

El siguiente patrón del que quiero hablar quizá no sea un patrón propio En realidad, es más bien una variación del patrón observador. Se llama "pub/sub" o "publicar-suscribir".

## PubSub vs. Observador

La diferencia clave entre observador y pub/sub es simplemente quién se encarga de notificar a los observadores. Con el patrón observador, es el sujeto, la cosa (como`GameApplication`) la que hace el trabajo. Con pub/sub, hay un tercer objeto -normalmente llamado "publicador"- cuyo único trabajo es gestionar este tipo de cosas, pero en lugar de llamarlo "publicador", voy a utilizar una palabra que probablemente te resulte más familiar: despachador de eventos.

Con pub/sub, los observadores (también llamados "oyentes") le dicen al despachador qué eventos quieren escuchar. Entonces, el sujeto (lo que está haciendo el trabajo) le dice al despachador que envíe el evento. El despachador se encarga entonces de llamar a los métodos de los oyentes.

Podrías argumentar que pub/sub sigue mejor el patrón de Responsabilidad Única. Despachar eventos y también registrar y llamar a los observadores son dos responsabilidades distintas que hemos metido en `GameApplication`.

## Crear el evento

Así que éste es el nuevo objetivo: añadir la posibilidad de ejecutar código antes de que comience una batalla utilizando pub/sub.

El primer paso es crear una clase de evento. Este será el objeto que se pasará como argumento a todos los métodos de escucha. Su propósito es prácticamente idéntico al de `FightResult` que pasamos a nuestros observadores: contiene cualquier dato que pueda ser útil para un oyente.

Con el patrón pub/sub, es habitual crear una clase de evento sólo para el sistema de eventos. Así que dentro de `src/`, voy a crear un nuevo directorio `Event/`. Luego una nueva clase PHP. Puedes llamarla como quieras, pero para este tutorial, vamos a llamarla `FightStartingEvent`:

[[[ code('98ca8c72bf') ]]]

Esta clase no necesita parecerse ni extender nada... y hablaremos más de ella en un minuto.

## Despachar el evento

El segundo paso es despachar este evento dentro de `GameApplication`. En lugar de escribir nuestro propio despachador de eventos, vamos a utilizar el de Symfony. Permíteme dividir el constructor en varias líneas... y luego añadir un nuevo`private EventDispatcherInterface $eventDispatcher`:

[[[ code('0217f86ee6') ]]]

Abajo en `play()`, justo en la parte superior, digamos `$this->eventDispatcher->dispatch()` pasando a`new FightStartingEvent()`:

[[[ code('8449cc9b42') ]]]

¡Ya está! Eso es suficiente para que el despachador notifique a todo el código que está a la escucha en `FightStartingEvent`. Por supuesto... ¡de momento no hay nada a la escucha!

## Registrar oyentes... Manualmente

Así que, finalmente, vamos a registrar un oyente para este evento. Abre `GameCommand`: el lugar donde estamos inicializando nuestra aplicación. Veremos cómo hacer todo esto correctamente con el contenedor de Symfony en un minuto, pero quiero que sea sencillo para empezar. En el constructor, añade `private readonly EventDispatcherInterface $eventDispatcher`:

[[[ code('5d9d5fc5d7') ]]]

Lo sé, estoy siendo un poco incoherente entre cuándo uso `readonly` y cuándo no. Técnicamente, podría usar `readonly` en todos los argumentos del constructor... pero no es algo que me importe demasiado. Sin embargo, queda muy bien.

## Elegir el EventDispatcherInterface correcto

Aquí abajo, en cualquier lugar antes de que nuestra aplicación se inicie realmente, digamos `$this->eventDispatcher->`. Observa que el único método que tiene es `dispatch()`. He cometido un... pequeño error. Volvamos atrás. En `GameApplication`, cuando autocableé `EventDispatcherInterface`, elegí el de `Psr\EventDispatcher\EventDispatcherInterface`, que contiene el método `dispatch()` que necesitamos. Así que está muy bien.

Dentro de `GameCommand`, autocableamos esa misma interfaz. Pero si quieres tener la posibilidad de adjuntar oyentes en tiempo de ejecución, tienes que autocablear`EventDispatcherInterface` desde `Symfony\Component\EventDispatcher` en lugar de `Psr`:

[[[ code('7bd4c3b975') ]]]

La de Symfony extiende la de `Psr`:

En realidad, independientemente de la interfaz que utilices, Symfony siempre nos pasará el mismo objeto. Ese objeto tiene un método en él llamado `addListener()`. Así que aunque hubiera utilizado la interfaz `Psr`, este método habría existido... sólo que se habría visto de forma extraña dentro de mi editor.

De todos modos, el primer argumento de esto es el nombre del evento, que va a coincidir con el nombre de la clase que estamos despachando. Así que podemos decir`FightStartingEvent::class`. Y luego, para simplificar, voy a ser perezoso y pasar un inline `function()`. También voy a `use ($io)`... para que dentro pueda decir`$io->note('Fight is starting...')`:

[[[ code('9baf26f7f3') ]]]

Y... ¡listo! Estamos enviando el evento dentro de `GameApplication`... y como hemos registrado el oyente aquí, ¡debería ser llamado!

¡Vamos a probarlo! En tu terminal, di:

```terminal
php ./bin/console app:game:play
```

Vamos a elegir nuestro personaje y... lo tenemos - `[NOTE] Fight is starting...`. Si volvemos a luchar... obtendremos el mismo mensaje. ¡Genial!

A continuación, hagamos esto más potente pasando información a nuestro oyente, como quién está a punto de combatir. Además, veremos cómo se utiliza el sistema de oyentes de eventos en una aplicación Symfony real, aprovechando el contenedor para conectarlo todo.
