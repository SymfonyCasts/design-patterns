# Estado en el mundo real

Bien, es hora de ver el patrón Estado en el mundo real: nuestro IDE. Abre `GameApplication`, y selecciona el método `play()`. Si intentamos refactorizarlo, veremos que la mayoría de las opciones están activadas. Si en cambio intentamos refactorizar una propiedad... la mayoría de las opciones están deshabilitadas: no podemos cambiar la firma ni hacerla estática. ¿Por qué? Las opciones disponibles se basan en lo que está seleccionado en el IDE. ¿Te suena? En este caso, el IDE es el contexto, y seleccionar algo es básicamente un "evento" o "acción" que hace que el IDE cambie su estado interno.

Otro lugar donde podemos ver el patrón de Estado en funcionamiento es en el componente Symfony Workflow. Este componente nos permite crear fácilmente máquinas de estado. Si echamos un vistazo a la documentación, podemos ver unos cuantos ejemplos. El que más me gusta es la función "pull request". Aquí podemos ver todos los estados posibles en los que puede estar una petición de extracción, cómo se pasa de un estado a otro y qué eventos se ejecutan por el camino. Si está en el estado "prueba", por ejemplo, y hay un evento "actualizar", permanece en el mismo estado. Pero si hay un evento "espera_revisión", pasa al estado "revisión". Genial, ¿verdad? ¡Y lo mejor es que podemos configurar nuestras máquinas de estado utilizando YAML! La próxima vez que necesites implementar una máquina de estados, te recomiendo encarecidamente que pruebes Symfony Workflow. Es un componente bastante divertido con el que trabajar.

## Estado vs. Estrategia

¿Sabías que los patrones Estado y Estrategia comparten el mismo diseño? La única diferencia estructural es que los objetos de estado pueden tener una referencia a otros estados. Entonces... si ambos patrones tienen el mismo diseño, ¿por qué importa cuál utilicemos? ¿No obtendremos el mismo resultado de cualquier forma? No exactamente. La diferencia más importante entre cada patrón es el propósito que hay detrás de ellos. El patrón Estado nos permite cambiar el comportamiento en función del estado interno de un objeto. El patrón Estrategia nos permite elegir entre una familia de algoritmos, independientemente del estado del sistema.

He aquí una excelente [analogía](https://bit.ly/state-vs-strategy) de Eugene Kovko y Michal Aibin:

> Un coche puede estar en diferentes estados. El motor puede
> estar encendido, y el motor puede estar apagado. La batería puede
> estar descargada. El depósito puede estar vacío, etc. En todos
> estos estados, el coche se comportará de forma diferente.
> Sin embargo, un conductor puede acceder a la interfaz del coche:
> volante, pedales, marchas, etc. Estos son los
> estados, y todo el comportamiento puede considerarse
> una combinación de los estados. Todos los estados
> proporcionarían un comportamiento distinto [...]

Este es el patrón Estado en acción. Pero, si cambiamos el motor para que use gasolina o gasóleo, no cambia el estado del coche, sólo cambia cómo funciona un elemento interno del sistema. Ese es el patrón Estrategia.

## Conclusión

Bien, ése es el patrón Estado en pocas palabras. Repasemos algunos de sus pros y contras:

✅ En primer lugar, el patrón Estado permite que tus objetos cambien de comportamiento cuando cambia su estado interno, e incluso puede ocultar en qué estado se encuentra el objeto. ✅ También es una forma estupenda de evitar el uso de grandes bloques if-else. ✅ Y, por último, aprovecha el SRP y el principio Abrir/Cerrar.

❌ Sin embargo, puede ser excesivo para casos sencillos ❌ y puede introducir un número importante de clases.

A continuación: Echaremos un vistazo al último patrón de esta serie: ¡el patrón Fábrica!
