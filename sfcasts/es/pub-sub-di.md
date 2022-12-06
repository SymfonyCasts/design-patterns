# Clase de evento Pub Sub y suscriptores en Symfony

Podemos ejecutar código justo antes de que comience una batalla registrando lo que se llama un "oyente" en `FightStartingEvent`. Como puedes ver, un oyente puede ser cualquier función... aunque lo que vemos aquí es un poco menos común. Normalmente un oyente será un método dentro de una clase. Y a eso lo refactorizaremos en unos minutos.

## Pasar datos a los oyentes

Pero antes de hacerlo, puede ser útil tener un poco más de información en nuestra función de escucha, como quién está a punto de combatir. Ese es el trabajo de esta clase de evento. Puede llevar los datos que queramos. Por ejemplo, crea un`public function __construct()` con dos propiedades... que voy a hacer públicas para simplificar: `$player` y `$ai`:

[[[ code('903f93271c') ]]]

¡Genial! En `GameApplication`, tenemos que pasar estas propiedades: `$player` y `$ai`:

[[[ code('c25b2d160e') ]]]

De vuelta a nuestro oyente, a esta función se le pasará un objeto `FightStartingEvent`. De hecho, siempre se le pasaba... sólo que antes no era útil. Ahora podemos decir `Fight is starting against`, seguido de `$event->ai->getNickname()`:

[[[ code('ad2eaad82b') ]]]

Muy bonito. ¡Pruébalo! Vuelvo a ejecutar el comando y... ¡qué bien! Vemos

¡> ! [NOTA] El combate está empezando contra la IA: Mago

Lo único que me falta es el espacio después de "contra" para que quede más bonito. Lo arreglaré rápidamente:

[[[ code('680f77c43f') ]]]

## Permitir que los oyentes controlen el comportamiento

Como ya he mencionado, realmente puedes poner los datos que quieras dentro de`FightStartingEvent`. Diablos, podrías crear una propiedad `public $shouldBattle = true` si quisieras. Entonces, en un oyente, podrías decir `$event->shouldBattle = false`... quizás porque los personajes han utilizado la comunicación y la honestidad para resolver sus problemas. ¡Un movimiento valiente!

De todos modos, en `GameApplication`, podrías entonces asignar este evento a un nuevo objeto `$event`, despacharlo, y si no deben luchar, simplemente `return`. O podrías`return new FightResult()` o lanzar una excepción. En cualquier caso, ya ves el sentido. Tus oyentes pueden, en cierto modo, comunicarse con el objeto central para controlar su comportamiento.

Todo esto lo haré dentro de `GameApplication`, `FightStartingEvent` y también`GameCommand`.

## Crear un suscriptor de eventos

A pesar de lo fácil que es este oyente en línea, es más habitual crear una clase independiente para tu oyente. Puedes crear una clase oyente, que es básicamente una clase que tiene este código aquí como función pública, o puedes crear una clase llamada suscriptor. Ambas son formas completamente válidas de utilizar el patrón pub/sub. La única diferencia es cómo se registra un oyente frente a un suscriptor, que es bastante menor, y lo verás en un minuto. Vamos a refactorizar a un suscriptor porque son más fáciles de configurar en Symfony.

En el directorio `Event/`, crea una nueva clase PHP llamada... qué tal...`OutputFightStartingSubscriber`, ya que este suscriptor va a indicar que una batalla está comenzando:

[[[ code('aa599a4224') ]]]

Los escuchadores de eventos no necesitan extender ninguna clase base ni implementar ninguna interfaz, pero los suscriptores de eventos sí. Necesitan implementar `EventSubscriberInterface`:

[[[ code('118c784a67') ]]]

Ve a "Código" -> "Generar" o `Command`+`N` en un Mac y selecciona "Implementar métodos" para generar `getSubscribedEvents()`:

[[[ code('d69a200d67') ]]]

¡Bien! Con un suscriptor de eventos, enumerarás los eventos a los que te suscribes justo dentro de esta clase. Así que diremos `FightStartingEvent::class => 'onFightStart'`:

[[[ code('5bdee2bbdf') ]]]

Esto dice

> Cuando ocurra el `FightStartingEvent`, quiero que llames al método `onFightStart()` 
> justo dentro de esta clase

Crea eso: `public function onFightStart()`... que recibirá un argumento`FightStartingEvent`:

[[[ code('ad40bd69ae') ]]]

Para las tripas de esto, ve a `GameCommand` y roba la línea `$io`:

[[[ code('4b72734719') ]]]

Por cierto, el objeto `$io` es un poco difícil de pasar de los comandos de la consola a otras partes de tu código... así que voy a ignorar esa complejidad aquí y crear uno nuevo con `$io = new SymfonyStyle(new ArrayInput([]), new ConsoleOutput()`:

[[[ code('b7440fd623') ]]]

Ahora que tenemos un suscriptor, de vuelta en `GameCommand`, ¡vamos a conectarlo! En lugar de `addListener()`, digamos `addSubscriber()`, y dentro de éste,`new OutputFightStartingSubscriber()`:

[[[ code('0c014a36c1') ]]]

¡Fácil! ¡Hora de probar! Salgo, elijo mi personaje y... ¡vaya! Funciona tan bien que sale dos veces. ¡Es increíble!

Pero... en serio, ¿por qué imprime dos veces? ¡Esto es, una vez más, gracias a la autoconfiguración! Cada vez que creas una clase que implementa`EventSubscriberInterface`, el contenedor de Symfony ya la está tomando y registrando en el `EventDispatcher`. En otras palabras, Symfony, internamente, ya está llamando a esta línea de aquí. Así que, ¡podemos eliminarla!

[[[ code('70a13c5624') ]]]

Supongo que esto responde a la pregunta de

> ¿Cómo utilizamos el patrón pub/sub en Symfony?

Simplemente crea una clase, haz que implemente `EventSubscriberInterface` y... ¡listo! Symfony la registrará automáticamente. Para enviar un evento, crea una nueva clase de evento y envía ese evento en cualquier parte de tu código.

Si intentamos esto de nuevo (primero saldré de la batalla)... sólo se despacha una vez. ¡Genial!

Y... ¿cuáles son las ventajas de pub/sub? Realmente son las mismas que las del observador, aunque, en la práctica, pub/sub es un poco más común... probablemente porque Symfony ya tiene este gran despachador de eventos. ¡La mitad del trabajo ya está hecho para nosotros!

A continuación, ¡vamos a sumergirnos en nuestro último patrón! Es uno de mis favoritos y, creo, el más potente de Symfony: El patrón decorador.
