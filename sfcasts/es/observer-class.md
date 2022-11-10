# La clase de observadores

Próximamente...

Ahora vamos a implementar un observador concreto que calcule cuánta XP debe ganar el ganador y detecte si el personaje debe subir de nivel. Pero antes, tenemos que añadir un par de cosas a la clase `Character` para ayudar a ello. En la parte superior, añade un `private
int $level` que será por defecto `1` y un `private int $xp` que será por defecto`0`. Aquí abajo un poco, añadiré `public function getLevel(): int` que será `return
$this->level`, y otra función conveniente llamada `addXp()` que aceptará el nuevo `$xpEarned`. Y en realidad necesitamos devolver el nuevo número XP, así que dentro diré`$this->xp += $xpEarned`, y `return $this->xp`. Finalmente, justo después de esto, voy a pegar un método más en llamado `levelUp()`. Este es el método que llamaremos cuando el personaje del usuario suba de nivel, y puedes ver que aumenta un poco los`level`, `maxHealth`, y `baseDamage`. También podemos subir de nivel el tipo de ataque y de armadura si queremos. ¡Perfecto!

Ahora estamos listos para implementar ese observador. Dentro del directorio `/src/Observer`, crea una nueva clase PHP. Llamémosla `XpEarnedObserver`. Todos nuestros observadores necesitan `implement` el `GameObserverInterface`, y voy a ir a Generar Código, o "comando" + "N" en un Mac, para implementar el método `onFightFinished()`. En las tripas reales de `onFightFinished()`, vamos a delegar el trabajo real en un servicio llamado `XpEarnedService`.

Si has descargado el código del curso, deberías tener un directorio `/tutorial` con `XpCalculator.php` dentro. Voy a copiar eso, y en `/src`, crearé un nuevo directorio`/Service` y lo pegaré dentro. Puedes comprobarlo si quieres, pero básicamente lo que hace es tomar el `Character` que ganó, el nivel del enemigo, y calcula cuánta XP debe conceder al personaje. Entonces, si son elegibles para subir de nivel, subirá de nivel a ese personaje.

En `XpEarnedObserver`, podemos utilizar eso. Crea un constructor para que podamos autoinstalar un `private readonly` (para que podamos ser súper modernos) `XpCalculator
$xpCalculator`. Luego, aquí abajo, pongamos el `$winner` en una variable -`$fightResult->getWinner()`, y pongamos el `$loser` en `$fightResult->getLoser()`. Abajo, digamos `$this->xpCalculator->addXp()` y pasemos a `$winner, $loser->getLevel()`. ¡Hermoso!

El sujeto y el observador de `GameApplication.php` están hechos. El último paso es instanciar el observador y hacer que se suscriba a `GameApplication.php`. Vamos a hacerlo manualmente dentro de `GameCommand.php`. Ve a`/src/Command/GameCommand.php`, y busca `execute()`, que es donde actualmente estamos inicializando todo el código dentro de nuestra aplicación. En unos minutos, veremos una forma más Symfony de conectar todo esto. Por ahora, diremos `$xpObserver =
new XpEarnedObserver()`. Le pasaremos un servicio `new XpCalculator()` para que esté contento. Luego, podemos decir `$this->game` (para usar la propiedad del juego)`->subscribe($xpObserver)`. Así que estamos suscribiendo el observador antes de ejecutar realmente nuestra aplicación aquí abajo.

¡Y ya estamos listos! Pero, sólo para que sea un poco más obvio si esto funciona, vuelve a `Character` y añade una función más aquí llamada `getXp()`, que devolverá `int`, y que será `return $this->xp`. Esto nos permitirá estar dentro de`GameCommand.php`. Si te desplazas un poco hacia abajo hasta `printResults()`... aquí vamos... añadiremos un par de cosas nuevas aquí como `$io->writeIn('XP: ' .
$player->getXp())`, y haremos lo mismo para `Final Level`, o el nivel en el que se encuentran después de terminar la batalla, `$player->getLevel()`.

Muy bien, ¡probemos esto! Gira, corre

```terminal
./bin/console app:game:play
```

y juguemos como el `fighter`, porque sigue siendo uno de los personajes más duros y... ¡impresionante! Como hemos ganado, hemos recibido 30 XP. Seguimos siendo de nivel 1, así que luchemos unas cuantas veces más. Aw... perdimos, así que no hay XP ahí. Ahora tenemos 60 XP... 90 XP... ¡guau! ¡Hemos subido de nivel! Dice `Final Level: 2`. ¡Está funcionando! Lo bueno de esto es que `GameApplication.php` no necesita saber ni preocuparse por la XP y la lógica de la subida de nivel. Sólo notifica a sus suscriptores y ellos pueden hacer lo que quieran.

A continuación, vamos a ver cómo podríamos conectar todo esto utilizando el contenedor de Symfony. También hablaremos de las ventajas de este patrón y de qué partes de SOLID ayuda.
