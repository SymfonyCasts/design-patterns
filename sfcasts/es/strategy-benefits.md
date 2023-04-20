# Estrategia Parte 2: Beneficios y en la naturaleza

Acabamos de utilizar el Patrón de Estrategia para permitir que cosas ajenas a la clase `Character` controlen cómo se producen los ataques, creando un `AttackType` personalizado... y pasándolo después al crear el `Character`.

## ¿Convenciones de nombres?

Si has leído sobre este patrón, puede que te preguntes por qué no hemos llamado a la interfaz `AttackStrategy` como el patrón. La respuesta es... porque no tenemos que hacerlo. En serio, la claridad y el propósito de esta clase son más valiosos que insinuar el nombre de un patrón. Si lo llamáramos "estrategia de ataque"... podría parecer que se encarga de planificar una estrategia de ataque. Eso no es lo que pretendíamos. De ahí nuestro nombre `AttackType`

[[[ code('1c5e5b3243') ]]]

## Otro ejemplo de patrón de estrategia

Hagamos otro ejemplo rápido de patrón de estrategia para equilibrar aún más nuestros personajes. Quiero poder controlar la armadura de cada personaje más allá del número que se está pasando ahora. Esto se utiliza en `receiveAttack()` para calcular en cuánto se puede reducir un ataque. Esto estaba bien antes, pero ahora quiero empezar a crear tipos de armadura muy diferentes que tengan cada uno propiedades distintas más allá de un simple número. Tendremos que actualizar nuestro código para permitir esto.

[[[ code('04f42f69bf') ]]]

Una vez más, podríamos resolverlo creando subclases, como`CharacterWithShield`. Pero ahora puedes ver por qué no es un buen plan. Si además hubiéramos utilizado la herencia para personalizar cómo se producen los ataques, podríamos acabar con clases como `TwoHandedSwordWithShieldCharacter` o`SpellCastingAndBowUsingWearingLeatherArmorCharacter`. ¡Yikes!

Así que, en lugar de navegar por esa pesadilla de subclases interminables, utilizaremos el Patrón de Estrategia. Repasemos los tres pasos anteriores. El primer paso es identificar el código que hay que cambiar y crear una interfaz para él.

En nuestro caso, tenemos que determinar en cuánto debe reducirse un ataque. Genial: crea un nuevo directorio `ArmorType/` y dentro de él, una nueva clase PHP... que en realidad será una interfaz... y llámala, qué tal, `ArmorType`.

Para alojar el código de reducción de la armadura, digamos `public function getArmorReduction()` donde pasaremos el `$damage` que vamos a hacer, y nos devolverá cuánta reducción de daño debe aplicar la armadura.

[[[ code('f4c215f899') ]]]

El segundo paso es crear al menos una implementación de esto. Crea una nueva clase PHP llamada `ShieldType` y haz que implemente `ArmorType`. A continuación, generaré el método`getArmorReduction()`. El escudo es genial porque va a tener un 20% de posibilidades de bloquear completamente un ataque entrante. Crea una variable `$chanceToBlock` con el valor `Dice::roll(100)`. Luego, si el `$chanceToBlock` es `> 80`, vamos a reducir todo el daño. Así que devuelve `$damage`. Si no, nuestro escudo no tendrá sentido y reducirá el daño en cero. ¡Ay!

[[[ code('25ef037256') ]]]

Ya que estamos aquí, vamos a crear otros dos tipos de armadura. La primera es una`LeatherArmorType`. Pondré la lógica: absorbe el 20% del daño.

[[[ code('d7e2620448') ]]]

Y luego crearé la genial `IceBlockType`: una cosita para nuestra gente mágica. También pegaré esa lógica. Esto absorberá dos tiradas de dados de ocho caras sumadas.

[[[ code('67947bca1a') ]]]

Vale, tercer paso: permite pasar un objeto de la interfaz `ArmorType` a`Character`... y luego utiliza su lógica. En este caso, no necesitaremos el número `$armor`en absoluto. En su lugar, añade un argumento `private ArmorType $armorType`.

[[[ code('643bf3e1ef') ]]]

Más abajo, en `receiveAttack()`, di`$armorReduction = $this->armorType->getArmorReduction()` y pasa `$damage`. Y para asegurarte de que las cosas no se desvían en negativo, añade un `max()` después de `$damageTaken`pasando `$damage - $armorReduction` y `0`.

[[[ code('77d9d5dcee') ]]]

Listo! `Character` ahora aprovecha el Patrón de Estrategia... ¡de nuevo! Vamos a aprovecharlo en `GameApplication`.

Empieza por eliminar el número de armadura en cada uno de ellos. Luego pasaré rápidamente a`ArmorType`: `new ShieldType()`, `new LeatherArmorType()`, y `new IceBlockType()`. Para nuestro `mage-archer`, que es nuestro personaje raro, lo mantendremos raro dándole un escudo - `new ShieldType()`. Y también tengo que asegurarme de quitar la armadura para eso también. ¡Perfecto!

[[[ code('8abe1cfcc0') ]]]

Vamos a probar este equipo. Dirígete y corre:

```terminal
./bin/console app:game:play
```

Y... ¡parece que funciona! Vamos a jugar como `mage-archer` y... ¡qué bien! Bueno, he perdido. Eso no es dulce, ¡pero me esforcé al máximo! Y puedes ver que el "daño infligido" y el "daño recibido" parecen seguir funcionando. ¡Es increíble!

## Beneficios del patrón

¡Así que ése es el Patrón de Estrategia! ¿Cuándo lo necesitas? Cuando te encuentres con que necesitas cambiar sólo una parte del código dentro de una clase. ¿Y cuáles son los beneficios? ¡Un montón! A diferencia de la herencia, ahora podemos crear personajes con infinitas combinaciones de comportamientos de ataque y armadura. También podemos intercambiar un `AttackType` o un `ArmorType`en tiempo de ejecución. Esto significa que podríamos, por ejemplo, leer alguna configuración o variable de entorno y utilizarla dinámicamente para cambiar uno de los tipos de ataque de nuestros personajes sobre la marcha. Eso no es posible con la herencia.

## Patrón y principio SOLID

Si has visto nuestro tutorial sobre SOLID, el Patrón de Estrategia es una clara victoria para SRP -el principio de responsabilidad única- y OCP -el principio de abierto-cerrado-. El Patrón de Estrategia nos permite dividir las clases grandes como `Character`en otras más pequeñas y centradas, pero que sigan interactuando entre sí. Eso complace a SRP.

Y OCP está contento porque ahora tenemos una forma de modificar o ampliar el comportamiento de la clase`Character` sin cambiar realmente el código que hay dentro. En su lugar, podemos pasar nuevos tipos de armadura y de ataque.

## El patrón de estrategia en el mundo real

Por último, ¿dónde podríamos ver este patrón en el mundo real? Un ejemplo, si pulsas "shift" + "shift" y escribes `Session.php`, es la clase `Session` de Symfony. El`Session` es un simple almacén de valores clave, pero diferentes aplicaciones necesitarán almacenar esos datos en diferentes lugares, como el sistema de archivos o una base de datos.

En lugar de intentar conseguirlo con un montón de código dentro de la propia clase `Session`, `Session` acepta un `SessionStorageInterface`. Podemos pasar cualquier estrategia de almacenamiento de sesión que queramos. Incluso podríamos utilizar variables de entorno para cambiar a un almacenamiento diferente en tiempo de ejecución

¿Dónde más se utiliza el patrón de estrategia? Bueno, es sutil, pero en realidad se utiliza en muchos sitios. Siempre que tengas una clase que acepte una interfaz como argumento del constructor, especialmente si esa interfaz procede de la misma biblioteca, es muy posible que se trate del Patrón de Estrategia. Significa que el autor de la biblioteca decidió que, en lugar de poner un montón de código en medio de la clase, debería abstraerse en otra clase. Y, al hacer una interfaz de tipo, están permitiendo que otra persona pase la implementación -o estrategia- que quiera.

He aquí otro ejemplo. En GitHub, estoy en el repositorio de Symfony. Pulsa "t" y busca `JsonLoginAuthenticator`. Este es el código detrás del autentificador de seguridad `json_login`. Una necesidad común con el `JsonLoginAuthenticator`es utilizarlo de forma normal... pero luego tomar el control de lo que ocurre en caso de éxito: por ejemplo, controlar el JSON que se devuelve tras la autenticación.

Para permitir eso `JsonLoginAuthenticator` te permite pasar un`AuthenticationSuccessHandlerInterface`. Así, en lugar de que esta clase intente averiguar qué hacer en caso de éxito, nos permite pasar una implementación personalizada que nos da el control total.

¿Crees que tienes todo eso? ¡Genial! Hablemos ahora del Patrón Constructor.
