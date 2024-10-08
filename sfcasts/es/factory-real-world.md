# El patrón Factory en el mundo real

Si estás tan entusiasmado como yo con el patrón Factory, es posible que quieras empezar a utilizarlo en tus aplicaciones Symfony. Afortunadamente, Symfony proporciona un soporte sólido.

## Usando Fábricas con Symfony

¡Compruébalo! Ve al sitio Symfony docs, busca "Using Factory", y haz clic en el primer enlace [aquí](https://symfony.com/doc/current/service_container/factories.html).

Esto nos muestra un montón de formas de utilizar una fábrica, como la creación de objetos a través de una "fábrica estática", que no es más que un método estático dentro de una clase de servicio. Y aquí abajo, podemos ver la configuración que necesitamos para establecer esto - utilizando la opción `factory` en la definición del cliente para especificar la clase de fábrica y el método estático a llamar. ¡Súper útil!

Otra opción es hacer que la clase que quieres instanciar tenga su propia fábrica. También funciona con un método estático, y es superútil para minimizar la cantidad de clases que tenemos, manteniendo la lógica de instanciación en un solo lugar. Aquí abajo, también tenemos la "Fábrica no estática", que es bastante similar a lo que hemos estado haciendo. La principal diferencia es que Symfony se encargará de instanciar la fábrica, llamar al método `make()` e inyectar lo que devuelva en el servicio que lo necesite. Ah, y si tu método `make()` requiere algunos argumentos, puedes utilizar la opción `arguments` para definirlos. Y si ninguna de estas opciones te funciona, siempre puedes inyectar tu fábrica en tus servicios y llamar tú mismo al método `make()`, tal y como hemos hecho en este tutorial.

## Fábrica en Symfony Forms

Bien, estas son algunas de las opciones que nos da Symfony para utilizar el patrón Factory. ¿Pero Symfony utiliza el patrón internamente? ¡Por supuesto! Symfony utiliza el patrón Fábrica en el componente Formulario.

Echemos un vistazo al código:

```php
class TaskController extends AbstractController
{
    public function new(Request $request): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);
        ...
    }
}
```

Se trata de un simple método controlador que recibe una petición, crea un objeto formulario llamando a `$this->createForm()`, y lo procesa. Ahora, veamos el método `createForm()`.

```php
class AbstractController implements ServiceSubscriberInterface
{
    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }
}
```

Se trata de un atajo para obtener el servicio `form.factory` del contenedor y llamar a `create()`. Si te fijas en los argumentos, recibe un parámetro`$type` que determina qué objeto formulario crear, así como algunos datos para rellenarlo. ¿Te resulta familiar? ¡También hicimos esto antes!

## Conclusión

¡Muy bien! ¡Ése es el patrón Fábrica! Repasemos algunos de sus pros y contras:

✅ En primer lugar, es una forma estupenda de desacoplar el código que crea objetos del código que los utiliza. ✅ También es muy útil cuando necesitas crear diferentes familias de objetos. ✅ Y, por último, aprovecha los principios de SRP y Abierto-cerrado.

❌ Pero puede hacer que tu base de código sea más compleja, sobre todo si tienes muchas fábricas.

¡Muy bien, equipo! ¡Hemos terminado! Hemos cubierto muchos patrones, pero aún hay más por ahí. Si hay algún patrón que te gustaría conocer, ¡háznoslo saber! Hasta entonces, diviértete poniendo todo esto en práctica, y si necesitas ayuda, estamos aquí para ti en los comentarios.

Gracias por codificar conmigo, ¡y hasta la próxima!
