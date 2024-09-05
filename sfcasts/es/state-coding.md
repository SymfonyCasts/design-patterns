# Manejar las dificultades con el patrón de estado

¿Cómo se manejan los niveles de dificultad en nuestra aplicación? Abre`GameCommand` y, dentro del método `play()`, encuentra la parte en la que comprobamos el resultado del partido. Si el jugador gana, llamamos a `victory()` en el objeto de juego, de lo contrario llamamos a `defeat()`. Echemos un vistazo al método `victory()`. Mantén pulsado "Comando", haz clic y... ¡oh! Es sólo un atajo para llamar a `victory()`en esta propiedad `difficultyContext`. Es una instancia de la clase`GameDifficultyContext`, y se encarga de gestionar los niveles de dificultad.

Mantén pulsado "Comando" y vuelve a hacer clic en el método `victory()` y... ajá - algo de código real. Aquí tienes una sentencia `switch-case` para aumentar el nivel de dificultad en función del nivel actual, así como algunas condiciones. Por ejemplo, para pasar del nivel de dificultad 1 al 2, el nivel del jugador debe ser al menos 2 o tiene que haber ganado dos combates. A continuación, endurece el juego aumentando algunas de las estadísticas de los enemigos. Pero, para mantenerlo justo y divertido, también aumenta la bonificación de XP del jugador. El nivel de dificultad 2 es bastante similar, pero las condiciones son simplemente más difíciles de cumplir. Y para el nivel 3, tenemos algo de aleatoriedad, tiramos un dado de 20 caras y, según el resultado, podemos aplicar algunas bonificaciones. ¡Genial! Por debajo de eso, tenemos el método`defeat()`, que es lo contrario de `victory()`. Si el jugador pierde, existe la posibilidad de que el nivel de dificultad disminuya, y si es así, restaura los ajustes de las bonificaciones.

[[[ code('322b9c1e1a') ]]]

De acuerdo El plan es refactorizar este código para que aproveche el patrón Estado. El primer paso es trasladar la lógica de cada nivel, o "estado", a su propia clase. Empecemos por crear una interfaz para nuestros estados. Dentro de `src/`, añade una nueva carpeta llamada `DifficultyState`, y dentro de ella, añade una nueva clase PHP - `DifficultyStateInterface`. La interfaz del estado debe tener un método para cada posible evento. En nuestro caso, serían `victory()` y `defeat()`, así que escribe`public function victory()`. Para los argumentos, escribe`GameDifficultyContext $difficultyContext`, `Character $player`, y`FightResult $fightResult`. El método `defeat()` tiene los mismos argumentos, así que podemos duplicar esta línea y renombrarla como "derrota".

[[[ code('1d09afc189') ]]]

Los argumentos `$player` y `$fightResult` podrían haberse envuelto en `DifficultyContext`, pero lo dejaremos así.

Muy bien, ya estamos listos para añadir algunos estados. Crea una nueva clase PHP dentro de la misma carpeta, y en lugar de utilizar números para representar los niveles de dificultad, vamos a nombrarlos: "Fácil", "Medio", etc. Así que llamémosla`EasyState`, hagamos que implemente la interfaz, y mantén pulsadas las teclas "opción" + "intro" para añadir los métodos.

[[[ code('d5732c9bee') ]]]

¡Perfecto! Cerraré algunas cosas, y luego, de vuelta en `GameDifficultyContext`, busca el primer caso en el método `victory()` y cópialo. Luego, en `EasyState`, pégalo y sustituye `$this` por `$difficultyContext`. También cambiaremos esta propiedad `level` para que haga referencia al objeto de estado actual, así que cámbiale el nombre a `difficultyState` y ponla en el siguiente estado: `new MediumState()`.

[[[ code('059e619889') ]]]

Aún no hemos creado esta clase, pero lo haremos dentro de un momento. Para añadir la propiedad, mantén pulsadas las teclas "opción" + "intro" y cambia su pista de tipo a `DifficultyStateInterface`.

[[[ code('1d34bda0eb') ]]]

¡Estupendo! Y si echamos un vistazo rápido a la función `defeat`, desplázate hacia abajo y... vale. No hay nada que hacer cuando un jugador es derrotado en `EasyState`, ya que es el nivel más bajo, así que podemos dejarlo como está.

Ahora vamos a refactorizar el nivel 2. Añade otra clase PHP y llámala `MediumState`. ¡La siguiente parte debería resultarte familiar! Implementaremos la interfaz... añadiremos los métodos manteniendo pulsadas las teclas "opción" + "intro"... copiaremos el código del nivel 2 en`GameDifficultyContext`... lo pegaremos en la clase `MediumState`, y arreglaremos el código. Este estado nos hará pasar a la dificultad "difícil", así que pon`difficultyState` en `new HardState()`. Eso tampoco existe todavía, pero ya llegaremos a ello. 

[[[ code('6cee45fbad') ]]]

Ahora podemos copiar el código del método `defeat()`, arreglarlo, y eso nos moverá de nuevo a la dificultad `EasyState`, así que establece `difficultyState` en`new EasyState()`.

[[[ code('bef31e53b8') ]]]

Por último, crearemos nuestro último estado de dificultad. Añade una nueva clase PHP llamada `HardState`, y volveremos a repetir el proceso: implementa la interfaz, genera los 2 métodos vacíos, luego copia las tripas que necesitamos para `victory()`y pégalas. Actualiza `$this->enemyLevelBonus` en la variable local `$difficultyContext`. Todo lo que hicimos antes.

[[[ code('46eec8ff42') ]]]

Para `defeat()`, coge su código de `GameDifficultyContext`, pégalo, y... haz ese renombramiento de variable una última vez. Y al final, ¡no olvides cambiar el `difficultyState` por `new MediumState()`!

[[[ code('eb45b4b759') ]]]

Uf... ¡ya casi lo tenemos! Ahora sólo tenemos que inicializar el nivel inicial. Añade un constructor a `GameDifficultyContext` y establece `difficultyState` en`new EasyState()`.

[[[ code('520a3c0644') ]]]

Y no olvides actualizar los métodos `victory()` y `defeat()` para que ahora llamen a la propiedad `difficultyState`. 

[[[ code('ee072270df') ]]]

Vale, ya estamos listos para probarlo, pero antes voy a hacer un poco de trampa para activar siempre el método `victory()`. En `GameApplication`, vamos a poner la salud del jugador a "100" al principio de cada ronda para que nunca perdamos. Al fin y al cabo, ¡soy el amo del juego! Ahora veamos si funciona.

Gira hasta tu terminal y ejecuta:

```terminal
php bin/console app:game:play
```

Jugaremos unas cuantas rondas hasta que ganemos, y... ¡sí! ¡Lo hemos conseguido! Pero necesitamos ganar dos batallas antes de poder subir de nivel, así que sigamos. Y... ¡woohoo! ¡Ahí está nuestro mensaje!

> ¡El nivel de dificultad del juego ha aumentado a Medio!

Esto funciona muy bien, pero puede que no te guste mucho cómo hemos instanciado esos objetos de estado. ¿Y si tuvieran algunas dependencias? ¿O si fueran caros de crear? ¡Buenas preguntas! Si los estados son sencillos de crear, haz lo que hemos hecho, porque simplificará tu código. Si tienen dependencias y son caros de crear, aprovecha el atributo [`AutowireLocator` ](https://bit.ly/sf-service-locator-attribute) para inyectarlos perezosamente y reutilizar la misma instancia. Si necesitas un objeto de estado nuevo cada vez, utiliza una fábrica para crearlos, e inyéctala en el `GameDifficultyContext`. Pronto hablaremos más sobre las fábricas.

¡Muy bien! Nuestra nueva configuración hace que sea superfácil añadir nuevos niveles al juego. Si quisiéramos añadir un nivel "más difícil", sólo tendríamos que añadir una nueva clase de estado y hacer un pequeño cambio en `HardState` para que nos suba al nivel "más difícil" en el método`victory()`. ¡Así de sencillo!

Siguiente: ¡Veamos el patrón Estado en el mundo real!
