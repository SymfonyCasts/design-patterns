# El patrón de fábrica abstracta

Vamos a subir de nivel nuestro `AttackTypeFactory` y convertirlo en una fábrica abstracta. Como vimos en el capítulo anterior, una fábrica abstracta nos permite manejar familias de objetos. Para ilustrar esto, vamos a introducir códigos de trucos en el juego que, si los activamos, nos darán unas armas superpoderosas. Oh sí, ¡ahora sí que podemos dominar el juego! Para añadir códigos de trucos, necesitaremos crear otra fábrica y una forma de intercambiarla en tiempo de ejecución. Así que ¡manos a la obra!

## Añadir más fábricas

Lo primero que tenemos que hacer es crear una interfaz para nuestras fábricas. En lugar de hacerlo manualmente, te mostraré un pequeño truco que hará que PhpStorm lo haga por ti. Abre `AttackTypeFactory`, haz clic con el botón derecho en el nombre de la clase, selecciona "Refactorizar" y luego "Extraer interfaz". Cambia el nombre a`AttackTypeFactoryInterface` y vuelve a hacer clic en "Refactorizar". Ahí está nuestra interfaz, y tiene un método `create()` tal y como queríamos. Y en `AttackTypeFactory`, ya está implementado. Muy práctico, ¿verdad?

[[[ code('6c51c575ce') ]]]

El siguiente paso es crear una nueva fábrica para el nuevo "conjunto" o "familia" de objetos`AttackType`. Dentro del directorio `Factory/`, añade una nueva clase PHP que llamaremos `UltimateAttackTypeFactory`. Implementa la interfaz... y añade el método`create()` manteniendo pulsadas las teclas "Opción" + "Intro" y seleccionando "Añadir stubs de métodos". Limpio esto y... ¡perfecto!

[[[ code('996f48d824') ]]]

Ahora, para el método `create()`, añadiremos una declaración `match` muy similar a la de `AttackTypeFactory`. Escribe `return match ($type)` y, dentro, utilizaremos los mismos casos pero devolveremos objetos `AttackType` diferentes. Por ejemplo, en el caso `bow`, devolveremos un arma más poderosa: un objeto`TitaniumBowType`. Pero... espera. Esa clase aún no existe, ¿verdad? ¡No! Pero para ahorrarnos algo de tiempo, esas nuevas clases `AttackType` ya están preparadas en el directorio `tutorial/`, en la raíz de nuestro proyecto. Ábrelo, copia la carpeta `Ultimate/` y pégala dentro de `src/AttackType/`.

¡Muy bien! ¡Vamos a terminar esto! Añade un caso `sword` y devuelve`new SkullBreakerSwordType()`. Para el caso `fire_bolt`, devuelve`new MeteorType()`. Y por último, para el caso `default`, lanza un`\RuntimeException()` con un mensaje - `Invalid attack type`. ¡Listo!

[[[ code('23aaf96b78') ]]]

A continuación, tenemos que añadir una forma de intercambiar nuestras fábricas en tiempo de ejecución. Abre`CharacterBuilder` y desplázate hasta su constructor. Podemos ver que ya tiene una dependencia con la clase concreta `AttackTypeFactory`. Necesitamos que funcione con cualquier fábrica, así que cambia su type hint a`AttackTypeFactoryInterface`. Después, para que sea intercambiable, necesitaremos un definidor para esta propiedad, así que elimina la sentencia `readonly` y añade el método definidor moviendo el cursor sobre el nombre de la propiedad, manteniendo pulsadas las teclas "Opción" + "Intro" y seleccionando "Añadir definidor".

[[[ code('049bcbb241') ]]]

## Añadir códigos trampa

¡Muy bien! ¡Nos estamos acercando! Ahora necesitamos una forma de reproducir nuestros códigos de trucos. Los manejaremos como opciones de línea de comandos, así que abre `GameCommand` y, debajo del constructor, escribe `protected function configure()`. Dentro, añade una nueva opción llamando a `$this->addOption()`. El primer argumento es el nombre de la opción. La llamaremos `cheatCode`. El segundo argumento es el atajo. Usaremos `c`. El tercer argumento es el modo, necesitamos que tenga un valor así que pongámoslo `InputOption::VALUE_REQUIRED`.

[[[ code('54702e783f') ]]]

A continuación, dentro del método `execute()`, antes de seleccionar el carácter, comprobaremos si se ha pasado la opción `cheatCode` y, en caso afirmativo, la activaremos.

Para ello, escribe `if ($input->getOption('cheatCode'))`, y dentro de éste,`$this->game->activateCheatCode()`, enviando la opción `cheatCode` como argumento. 

[[[ code('f08c591538') ]]]

Este método aún no existe, así que vamos a crearlo. Sitúa el cursor sobre el nombre del método, mantén pulsadas las teclas "Opción" + "Intro" y selecciona "Añadir método". Bien, cambia el argumento a `string $cheatCode`... y dentro, utilizaremos un`switch-case` por si queremos añadir más códigos de trucos en el futuro. Para ello, di `switch ($cheatCode)` y dentro de él, añadiremos un `case` con el valor de nuestro último código de trucos. Hm... ¿cuál sería un buen valor para eso? ¡Oh! ¡Ya sé! Voy a pegar esto porque es un poco largo, pero puede que te resulte familiar. ¿Recuerdas el famoso código Konami? ¡Parece que han vuelto los 90!

[[[ code('2ee859678d') ]]]

Bien, dentro del `case`, vamos a imprimir un mensaje para que sepamos que se ha activado el código de trucos. Luego cambiaremos la fábrica en el `CharacterBuilder`. Para ello, escribe`$this->characterBuilder->setAttackTypeFactory(new UltimateAttackTypeFactory())`y añade un `break` al final. Impresionante.

[[[ code('f0b0a2ce76') ]]]

Ahora, puede que estés pensando "¿Y si el `UltimateAttackTypeFactory` tiene dependencias?" o "¿Y si no es tan sencillo de instanciar?", y es una preocupación válida. Una forma de resolverlo es la misma que comentamos con las clases "estado": aprovechando el atributo [`AutowireLocator` ](https://bit.ly/sf-service-locator-attribute). Otra opción sería crear una fábrica para tus fábricas. ¡Ohh fábrica-cepción! Espero que Skynet no esté escuchando... Vale, podemos terminar esto añadiendo un caso `default` e imprimir un mensaje `Invalid Cheat Code`. ¡Perfecto!

[[[ code('46cf4014ef') ]]]

Antes de probarlo, hay un pequeño detalle que tenemos que solucionar. Symfony no sabe qué `AttackTypeFactory` inyectar en `CharacterBuilder` porque tenemos más de una implementación de `AttackTypeFactoryInterface`. Tenemos que decirle a Symfony cuál utilizar por defecto. Para ello, podemos aprovechar el atributo `AsAlias`. Abre `AttackTypeFactory` y, encima del nombre de la clase, escribe `#[AsAlias()]` y pasa `AttackTypeFactoryInterface::class` como ID. ¡Listo!

[[[ code('175de6b4a4') ]]]

¡Vamos a probarlo! Ve a tu terminal y ejecuta:

```terminal
php bin/console app:game:play -c up-up-down-down-left-right-left-right-b-a-start
```

¡Eh! ¡Mira eso! Ahí está nuestro mensaje `Ultimate cheat code activated!`. Y si luchamos... ¡ganamos en sólo dos rondas! ¡Códigos trampa para la victoria!

A continuación: Veamos cómo se utiliza el patrón de fábrica en el mundo real.
