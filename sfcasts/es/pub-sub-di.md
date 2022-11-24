# Clase de Sub Evento Pub y Suscriptores en Symfony

Próximamente...

Pero sería más genial si tuviéramos un poco más de información dentro de nuestro oyente, como por ejemplo quién ganó la batalla. Ese es el trabajo de esta clase de evento. Puede llevar los datos que queramos. Por ejemplo, vamos a crear un `public function __construct()`. Para simplificar, voy a crear dos propiedades de`public` llamadas `$player` y `$ai`. ¡Espectacular! En `GameApplication`, tenemos que pasarlas, así que pasa `$player` y `$ai` aquí. Entonces, en nuestro oyente, nuestra función recibirá un objeto `FightStartingEvent`. Siempre ha sido así, sólo que antes no era muy útil. Ahora podemos decir `Fight is starting
against`, seguido de `$event->ai->getNickname()`. Muy bonito. Vamos a probarlo! Ejecutaré de nuevo el comando y... ¡qué bien! Vemos `[NOTE] Fight is starting againstAI:
Mage`. Lo único que he hecho es omitir el espacio después de "contra" para que quede más bonito. Lo arreglaré rápidamente.

Como ya he dicho, puedes poner los datos que quieras en este`FightStartingEvent`. Diablos, podrías crear una propiedad `public $shouldBattle =  true`si quisieras. Y para tu oyente, podrías decir `$event->shouldBattle
= false`. En `GameApplication`, podrías realmente establecer este evento en un nuevo objeto `$event`, despacharlo, y si no deben batallar, simplemente `return`. O podrías `return FightResults()` y quizás lanzar una excepción. En cualquier caso, ya ves el sentido. Incluso puedes enviar señales de vuelta sobre lo que quieres hacer. Desharé todo eso dentro de `GameApplication`, `FightStartingEvent` y también `GameCommand`.

A pesar de lo fácil que es este oyente en línea, es más común crear una clase separada para tu oyente. Puedes crear una clase oyente, que es básicamente una clase que tiene este código aquí como función pública, o puedes crear una clase llamada suscriptor. Ambas son formas completamente válidas de utilizar el patrón pub/sub. La única diferencia es cómo se registra un oyente frente a un suscriptor, que es bastante menor, y lo verás en un minuto. Así que vamos a refactorizar esto a un suscriptor.

En el directorio `/Event`, crea una nueva clase PHP llamada... qué tal...`OutputFightStartingSubscriber`, ya que este suscriptor va a indicar que está empezando una batalla. Los escuchadores de eventos no necesitan extender ninguna clase base ni implementar ninguna interfaz, pero los suscriptores de eventos sí. Necesitan implementar la`EventSubscriberInterface`. Entonces iré a "Generar Código" o "Comando" + "N" en un Mac, iré a ""Implementar Métodos"", y seleccionaré `getSubscribedEvents()`. ¡Qué bien! Con un suscriptor de eventos, enumerarás los eventos a los que te suscribes justo dentro de esta clase. Así que diremos `FightStartingEvent::class => 'onFightStart'`. Esto dice básicamente:

`When that event happens, I want you to call the
onFightStart method in this class`.

Así que lo crearemos con `public function onFightStart(FightStartingEvent $event)`. Esto obtendrá el objeto `FightStartingEvent`. Luego, para las tripas de esto, iré a `GameCommand` y robaré nuestro `$io`. Es importante tener en cuenta que el `$io` es un poco difícil de pasar de los comandos de la consola a otras partes de tu código. Voy a ignorar esa complejidad aquí y simplemente crearé un nuevo `$io` diciendo `$io = new
SymfonyStyle(new ArrayInput([]), new ConsoleOutput()`. Estoy creando un objeto que es igual que el objeto normal `$io` para poder hacer un poco de trampa aquí.

Ahora que tenemos un suscriptor, en `GameCommand`, vamos a conectarlo. Así que en lugar de `addListener()`, digamos `addSubscriber()`, y dentro de eso, `new
OutputFightStartingSubscriber()`. ¡Genial!

¡Hora de probar! Momento de la verdad. Salgo, elijo mi personaje y... ¡guau! Está funcionando tan bien que sale dos veces. ¿Por qué? Esto es, una vez más, gracias a la autoconfiguración. Estamos utilizando la configuración automática dentro de nuestra aplicación, así que cada vez que creamos una clase que implementa `EventSubscriberInterface`, el contenedor de Symfony está tomando automáticamente ese suscriptor y registrándolo en el `eventDispatcher`. En otras palabras, Symfony, internamente, ya está llamando a esta línea de aquí. Así que eso responde a la pregunta de cómo usamos el patrón pub/sub en Symfony.

Iré a borrar esa línea dentro de `GameCommand`. Todo lo que tienes que hacer es crear un suscriptor de eventos como hemos hecho aquí, y Symfony lo registrará automáticamente. Luego, para despachar un evento, crearás una nueva clase de evento y despacharás ese evento en cualquier parte de tu código.

Así puedes ver lo fácil que es crear el `eventDispatcher` y despachar todo tipo de eventos por toda tu aplicación. Si probamos esto de nuevo (primero saldré de la batalla)... sólo despacha una vez. ¡Genial! Las ventajas de pub/sub son realmente las mismas que las del observador, pero en la práctica, pub/sub es un poco más común. Eso es probablemente porque Symfony tiene este gran despachador de eventos. La mitad del trabajo ya está hecho por ti.

A continuación, ¡vamos a sumergirnos en nuestro último patrón! Es uno de mis favoritos y el más potente de Symfony: El patrón decorador.
