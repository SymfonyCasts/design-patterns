# La clase observadora

Ahora que hemos terminado nuestra clase objeto - `GameApplication` - en la que podemos llamar a `subscribe()` si queremos que nos avisen cuando termine un combate - pasemos a crear un observador que calcule cuánta XP debe ganar el ganador y si el personaje debe subir de nivel o no.

Pero antes, tenemos que añadir algunas cosas a la clase `Character` para ayudar. En la parte superior, añade `private int $level` que será por defecto `1` y un `private int $xp` que será por defecto `0`:

[[[ code('d74fd395f2') ]]]

Aquí abajo un poco, añade `public function getLevel(): int` que será`return $this->level`... y otro método de conveniencia llamado `addXp()`que aceptará el nuevo `$xpEarned` y devolverá el nuevo número XP. Dentro digamos`$this->xp += $xpEarned`... y `return $this->xp`:

[[[ code('136a0fe3ec') ]]]

Por último, justo después, voy a pegar un método más llamado `levelUp()`. Lo llamaremos cuando un personaje suba de nivel: aumenta los `$level`, `$maxHealth`, y `$baseDamage`:

[[[ code('2fed6787cd') ]]]

También podríamos subir de nivel los tipos de ataque y armadura si quisiéramos.

## Creación de la clase observadora

Bien, ahora vamos a crear el observador. Dentro del directorio `src/Observer/`, añade una nueva clase PHP. Llamémosla `XpEarnedObserver`. Y todos nuestros observadores necesitan`implement` el `GameObserverInterface`. Ve a "Generar código", o `Command`+`N`en un Mac para implementar el método `onFightFinished()`:

[[[ code('2896640a22') ]]]

Para las tripas de `onFightFinished()`, voy a delegar el trabajo real en un servicio llamado `XpCalculator`.

Si has descargado el código del curso, deberías tener un directorio `tutorial/` con`XpCalculator.php` dentro. Copia eso, en `src/`, crea un nuevo directorio`Service/` y pégalo dentro. Puedes comprobarlo si quieres, pero no es nada del otro mundo:

[[[ code('94d6787e7c') ]]]

Toma el `Character` que ganó, el nivel del enemigo, y calcula cuánta XP debe otorgar al ganador. Luego, si son elegibles para subir de nivel, sube de nivel a ese personaje.

En `XpEarnedObserver`, podemos utilizar esto. Crea un constructor para que podamos autoinstalar un `private readonly` (`readonly` sólo para estar súper a la moda) `XpCalculator
$xpCalculator`:

[[[ code('1c0c2a8fb5') ]]]

A continuación, pongamos el `$winner` en una variable - `$fightResult->getWinner()` - y`$loser` en `$fightResult->getLoser()`. Por último, digamos `$this->xpCalculator->addXp()`y pasemos `$winner` y `$loser->getLevel()`:

[[[ code('b20527cbbb') ]]]

## Conectando el sujeto y el observador

¡Qué bien! El sujeto y el observador ya están hechos. El último paso es instanciar el observador y hacer que se suscriba al sujeto: `GameApplication`. Vamos a hacerlo manualmente dentro de `GameCommand`.

Abre `src/Command/GameCommand.php`, y busca `execute()`, que es donde actualmente estamos inicializando todo el código dentro de nuestra aplicación. En unos minutos, veremos una forma más Symfony de conectar todo esto. Por ahora, digamos`$xpObserver = new XpEarnedObserver()`... y pasemos que un servicio `new XpCalculator()` para que esté contento. Entonces, podemos decir `$this->game` (que es el `GameApplication`)`->subscribe($xpObserver)`:

[[[ code('b5f9e14530') ]]]

Así que estamos suscribiendo el observador antes de ejecutar nuestra aplicación aquí.

Esto significa que... ¡estamos listos! Pero, para que sea un poco más evidente si esto funciona, vuelve a `Character` y añade una función más aquí llamada `getXp()`, que devolverá `int` a través de `return $this->xp`:

[[[ code('1f3c26341a') ]]]

Esto nos permitirá, dentro de `GameCommand`... si te desplazas un poco hacia abajo hasta`printResults()`... aquí vamos... añadir algunas cosas como`$io->writeIn('XP: ' . $player->getXp())`... y lo mismo para `Final Level`, con `$player->getLevel()`:

[[[ code('41aedf9c68') ]]]

Bien, equipo, ¡es hora de probar! Gira, corre

```terminal
./bin/console app:game:play
```

y juguemos como el `fighter`, porque sigue siendo uno de los personajes más difíciles y... ¡impresionante! Como hemos ganado, hemos recibido 30 XP. Seguimos siendo de nivel 1, así que luchemos unas cuantas veces más. Aw... perdimos, así que no hay XP. Ahora tenemos 60 XP... 90 XP... ¡guau! ¡Hemos subido de nivel! Dice `Final Level: 2`. ¡Está funcionando!

Lo bueno de esto es que `GameApplication` no necesita saber ni preocuparse por la XP y la lógica de la subida de nivel. Sólo avisa a sus suscriptores y ellos pueden hacer lo que quieran.

A continuación, vamos a ver cómo podríamos conectar todo esto utilizando el contenedor de Symfony. También hablaremos de las ventajas de este patrón y de las partes de SOLID a las que ayuda.
