# Decoración: Anular los servicios del núcleo y AsDecorator

En Symfony, la decoración tiene un superpoder secreto: nos permite personalizar casi cualquier servicio dentro de Symfony.

Por ejemplo, imagina que hay un servicio del núcleo de Symfony y necesitas ampliar su comportamiento con el tuyo propio. ¿Cómo podrías hacerlo? Bueno, podríamos subclasificar el servicio del núcleo... y reconfigurar las cosas para que el contenedor de Symfony utilice nuestra clase en lugar de la del núcleo. Eso podría funcionar, pero aquí es donde brilla la decoración.

Así que, como reto, vamos a ampliar el comportamiento del servicio central de Symfony `EventDispatcher`para que cada vez que se despache un evento, volquemos un mensaje de depuración.

## Investigando el despachador de eventos

El ID del servicio que queremos decorar es `event_dispatcher`. Y, afortunadamente, esta clase implementa una interfaz. En GitHub... en el repositorio`symfony/symfony`, pulsa "t" y abre `EventDispatcher.php`.

Y... ¡sí! Esto implementa `EventDispatcherInterface`. ¡La decoración funcionará!

## Creación de la clase decoradora

Así que vamos a crear nuestra clase decoradora personalizada. Crearé un nuevo directorio `Decorator/`... y dentro, una nueva clase PHP llamada... ¿qué tal`DebugEventDispatcherDecorator`.

El primer paso, es siempre implementar la interfaz: `EventDispatcherInterface`... ¡aunque esto es un poco complicado porque hay tres! Está `Psr`, que es la más pequeña.. la de `Contract`, y esta otra de `Component`. La de `Component` extiende la de `Contact`... que extiende la de `Psr`.

En realidad, el más "grande": el de `Symfony/Component`. La razón es que, si nuestro decorador de `EventDispatcher` va a pasar por el sistema en lugar del real, necesita implementar la interfaz más fuerte: la que tiene más métodos.

Ve a Generar Código -o "comando" + "N" en un Mac- y selecciona "Implementar Métodos" para implementar el montón que necesitábamos. Uf... ¡ya está!

Lo otro que tenemos que hacer es añadir un constructor al que se le pasará el`EventDispatcherInterface` interior... y hacer que sea una propiedad con`private readonly`.

¡Perfecto! Ahora que tenemos esto, tenemos que llamar al despachador interno en todos estos métodos. Esta parte es sencilla.... pero aburrida. Digamos que`$this->eventDispatcher->addListener($eventName, $listener, $priority)`.

También tenemos que comprobar si el método debe devolver un valor o no. No necesitamos devolver en este método... pero hay métodos aquí abajo que sí tienen valores de retorno, como `getListeners()`.

Para no pasarme los próximos 3 meses repitiendo lo que acabo de hacer 8 veces más, voy a borrar todo esto y a pegar una versión acabada: puedes copiarlo del bloque de código de esta página. Simplemente estamos llamando al despachador interno en cada método.

Por último, ahora que nuestro decorador está haciendo todo lo que debe hacer, podemos añadir funcionalidad personalizada. Justo antes de llamar al método interno `dispatch()`, pegaré dos llamadas a `dump()` y también volcaré `Dispatching event`, `$event::class`.

## AsDecorator: Haciendo que Symfony utilice nuestro servicio

¡Muy bien! ¡Nuestra clase decoradora está hecha! Pero, hay muchos lugares en Symfony que dependen del servicio cuyo ID es `event_dispatcher`. Así que aquí está la pregunta del millón: ¿cómo podemos reemplazar ese servicio con nuestro propio servicio... pero seguir obteniendo el despachador de eventos original que nos han pasado?

Pues bien, Symfony tiene una función creada específicamente para esto, ¡y te va a encantar! Ve a la parte superior de nuestra clase decoradora y añade un atributo de PHP 8 llamado:`#[AsDecorator()]` y pasa el id del servicio que queremos decorar:`event_dispatcher`.

Eso es todo. Esto dice:

> Oye Symfony, por favor, hazme el servicio real `event_dispatcher`, pero sigue
> autocablea el servicio original `event_dispatcher` en mí.

¡Vamos a probarlo! Ejecuta nuestra aplicación:

```terminal-silent
php bin/console app:game:play
```

Y... ¡funciona! Mira! ¡Puedes ver cómo se vierte el evento! Y también está nuestro evento personalizado. Y si salgo... ¡otro evento al final! Acabamos de sustituir el servicio central de `event_dispatcher` por el nuestro creando una sola clase. ¡Esto es una maravilla!

## Usando AsDecorator con OutputtingXpCalculator

¿Podríamos haber utilizado antes este truco de `AsDecorator` para nuestra propia situación de decoración de `XpCalculator`? Sí He aquí cómo: En `config/services.yaml`, elimina los argumentos manuales y cambia la interfaz para que apunte al servicio original no decorado:`XpCalculator`. Básicamente, en la configuración del servicio, queremos configurar las cosas de la manera "normal", como si no hubiera decoradores.

Si probáramos ahora nuestra aplicación, funcionaría, pero no utilizaría nuestro decorador. Pero ahora, entra en `OutputtingXpCalculator` añade `#[AsDecorator()]` y pásale`XpCalculatorInterface::class`, ya que ése es el ID del servicio que queremos decorar.

¡Donezo! Si probamos esto ahora:

```terminal-silent
php bin/console app:game:play
```

No hay errores. Una forma aún más rápida de comprobar que esto funciona es ejecutando

```terminal
php bin/console debug:container XpCalculatorInterface --show-arguments
```

Si ejecutamos esto... ¡compruébalo! Dice que esto es un alias del servicio`OutputtingXpCalculator`. Así que cualquiera que esté autocableando esto está recibiendo realmente el servicio`OutputtingXpCalculator`. Y si miras aquí abajo los argumentos, el primer argumento que se pasa a `OutputtingXpCalculator` es el verdadero `XpCalculator`. ¡Esto es increíble!

## Decoración múltiple

Muy bien, el patrón decorador está hecho. ¡Qué patrón más chulo! Una característica del patrón decorador que sólo hemos mencionado es que puedes decorar un servicio tantas veces como quieras. ¡Sí! Si creamos otra clase que implemente`XpCalculatorInterface` y le damos este atributo `AsDecorator`, ahora habría dos servicios que la decorarían. ¿Qué servicio estaría en el exterior? Si te importa lo suficiente, podrías establecer una opción `priority` en uno de los atributos para controlarlo.

La mayor limitación del patrón decorador es simplemente que sólo puedes ejecutar código antes o después de un método. No podemos, por ejemplo, entrar en las tripas de ninguno de los métodos del núcleo `EventDispatcher` y cambiar su comportamiento. La decoración tiene un poder limitado.

## ¿Decoración en la naturaleza?

¿Dónde vemos la decoración en la naturaleza? La respuesta es... más o menos por todas partes. En la Plataforma API, es muy común utilizar la decoración para ampliar los comportamientos del núcleo, como el ContextBuilder. Y el propio Symfony utiliza la decoración con bastante frecuencia para añadir funciones de depuración mientras estamos en el entorno `dev`. Por ejemplo, sabemos que esta clase `EventDispatcher` se utilizaría en el entorno `prod`. Pero en el entorno dev - voy a darle a la "T" para buscar un "TraceableEventDispatcher" - suponiendo que tengas algunas herramientas de depuración instaladas, esta es la clase real que representa el `event_dispatcher`. ¡Decora el real!

Puedo demostrarlo. Vuelve a tu terminal y ejecuta

```terminal
./bin/console debug:container event_dispatcher --show-arguments
```

Desplázate hasta la parte superior y... ¡compruébalo! El servicio `event_dispatcher` es un alias de `debug.event_dispatcher`... ¡cuya clase es `TraceableEventDispatcher`! Y si te desplazas hasta sus argumentos, ¡ja! Ha pasado nuestro `DebugEventDispatcherDecorator`. como argumento. Sí, en este caso hay 3 despachadores de eventos: El núcleo de Symfony `TraceableEventDispatcher` está en el exterior, llama a nuestro`DebugEventDispatcherDecorator`... y éste, en última instancia, llama al despachador de eventos real. ¡Inicio!

## Problemas resueltos por el decorador

¿Y qué problemas resuelve el patrón decorador? Sencillo: nos permite ampliar el comportamiento de una clase existente -como `XpCalculator` - aunque esa clase no contenga ningún otro punto de ampliación. Esto significa que podemos utilizarlo para anular los servicios del proveedor cuando todo lo demás falla. El único inconveniente del patrón decorador es que sólo podemos ejecutar código antes o después del método principal. Y el servicio que queremos decorar debe implementar una interfaz.

Bien, equipo. ¡Ya hemos terminado! Hay muchos más patrones por ahí en la naturaleza: ésta ha sido una recopilación de algunos de nuestros favoritos. Si nos hemos saltado uno o varios de los que realmente quieres oír hablar, ¡háznoslo saber! Hasta entonces, comprueba si puedes detectar estos patrones en la naturaleza y averiguar dónde puedes aplicarlos para limpiar tu propio código... e impresionar a tus amigos.

Gracias por codificar conmigo, ¡y nos vemos la próxima vez!
