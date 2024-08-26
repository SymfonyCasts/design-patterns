# El primo de la Cadena de Responsabilidad: el patrón Middleware

¿Sabías que el patrón Cadena de Responsabilidad tiene un primo? Se llama patrón Middleware. Los dos patrones son muy parecidos, pero el patrón Middleware tiene una sutil diferencia: este patrón siempre llega al final de la cadena. En otras palabras, siempre se ejecuta cada uno de los manejadores (o middleware). Es una forma genial de introducir más acciones o lógica al procesar una petición. Por ejemplo, si utilizas un middleware para gestionar las inscripciones de usuarios, podrías tener un middleware para enviar análisis a una API, otro para rastrear un parámetro de consulta en la petición con fines de marketing, etc.

En nuestra aplicación de juego, podríamos utilizarlo para terminar de construir nuestros objetos personaje. Podríamos tener un middleware que diera un arma aleatoria al personaje, o que aumentara su nivel, o incluso que potenciara su salud. En resumen, todos los middlewares se ejecutarán, y utilizarlos es una forma estupenda de hacer que tu aplicación sea más flexible.

## Cadena de responsabilidad en el mundo real - Votantes Symfony

Bien, antes de pasar al siguiente patrón, echemos un vistazo a un ejemplo del mundo real. Symfony aprovecha la Cadena de Responsabilidad en su componente de seguridad. Utiliza un concepto llamado "Votantes" para determinar si un usuario tiene acceso a un recurso específico o no. Pero Symfony implementa este patrón de una forma ligeramente diferente. En lugar de tener referencias explícitas a otros votantes, Symfony maneja internamente la iteración y ejecución de los votantes en la cadena - los votantes en sí no tienen referencias directas a otros votantes en la cadena.

Echemos un vistazo más de cerca a cómo se utilizan los Votantes en Symfony. En tu navegador, ve a GitHub y busca el repositorio `symfony/security-core`. Yo ya lo tengo abierto. Ahora, busca la clase `AccessDecisionManager`. Si nos fijamos en su constructor, podemos ver que recibe una lista de votantes, y en algún momento, itera sobre ellos y llama a `vote()`. Esta clase hace mucho más que eso, por supuesto, pero podemos ver la esencia de la Cadena de Responsabilidad en funcionamiento aquí.

## Conclusión

Bien, ¡este es el patrón! Sencillo, ¿verdad? Repasemos algunos pros y contras:

✅ Lo más importante es que te permite añadir o eliminar manejadores dinámicamente cambiando los miembros o el orden de la cadena. ✅ También aprovecha el Principio de Responsabilidad Única, que nos permite desacoplar las clases que hacen el trabajo de las clases que las utilizan. ✅ Y, por último, aprovecha el Principio Abierto/Cerrado. Que nos permite introducir nuevos manejadores en la aplicación sin tocar el código existente.

❌ Pero, como ya hemos visto, puede ser difícil de depurar. ❌ Las peticiones también pueden acabar sin gestionar si ningún objeto las gestiona. ❌ Y, por desgracia, si la cadena es demasiado larga, puede afectar al rendimiento.

Así que ¡ahí lo tienes! Lo siguiente: Pasemos al patrón de Estado y manejemos los niveles de dificultad con estilo.
