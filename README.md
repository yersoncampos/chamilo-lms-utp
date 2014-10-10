Linea guia para Git
===================
Lineamientos para el desarrollo e implementacion de nuevas características y/o parches. Y la estructura de archivos que se maneja en el desarrollo de la copia de chamilo de Beeznest que tiene el GrupoUTP.


Estructura de desarrollo
------------------------
A continuacion veremos la estrucutra de carpeta de desarrollo.

**dev**: es la carpeta principal de desarrollo, contiene las fuentes, librerias y pruebas unitaria. Que son necesarias para la creacion de nuevos componentes y/o modelos. La carpeta **dev** contiene a su vez 2 subcarpetas: **chamilo** y **vendor**.

**chamilo**: es la subcarpeta que contiene las fuentes o codigo que se ha desarrollado, es decir componentes y/o modelos. La subcarpeta **chamilo** contiene a su vez 3 subcarpetas: **conf**, **src** y **test**.

**conf**: es la subcarpeta que contiene todos los archivos de configuración para los componentes y/o modelos.

**src**: es la subcarpeta que contiene las fuentes de los componentes y/o modelos.

**test**: es la subcarpeta que contiene todas las pruebas unitarias de los componentes y/o modelos.

**vendor**: es la subcarpeta que contiene todas las librerias que se van a utilizar para el desarrollo de nuevos componentes, modelos o parches.

**Gráfico de la estructura**

```
+-- dev
|   +-- chamilo
|   |   +-- conf
|   |   +-- src
|   |   +-- test
|   +-- vendor
```

Flujo de trabajo en Git
=======================
El flujo de trabajo que se usara en git, es el flujo de trabajo bifurcado(Forking Workflow). 
Para entender mas sobre flujos de trabajo en git visitar https://www.atlassian.com/es/git/workflow.

Referencias en español
----------------------
**Flujo de trabajo bifurcado**: https://www.atlassian.com/es/git/workflows#!workflow-forking

**Guia basica de Git**: http://git-scm.com/book/es

**Emojis en Git**: http://www.emoji-cheat-sheet.com


Referencias en ingles
---------------------
**Contribuyendo a un proyecto de codigo abierto**: https://guides.github.com/activities/contributing-to-open-source

**Proyectos bifurcados**: https://guides.github.com/activities/forking

**Dominando problemas**: https://guides.github.com/features/issues

**Dominando markdown**: https://guides.github.com/features/mastering-markdown

**Mas guias en**: https://guides.github.com

Estandares para commits:
----------------------
A continuación veremos los estandares para realizar commits, y colaborar con el proyecto.

**para archivos en general**: se deben seguir ciertas practicas

* Los commits deben ir en ingles
* Los commits deben ser atomicos
* Los commits deben ser breves y concisos. Deben expresar la accion.
* Los commits deben tener estar relacionados a un solo problema.
* Los commits deben llevar el nombre del componente y el tipo de componente si la accion es agregar
* Los commits deben llevar el nombre del componente, el tipo de componente y el motivo, si la accion es eliminar
* Los commits deben llevar la version del componente, si el tipo de componente es una libreria
* Los commits para parches(bugs fixed) deben llevar en donde se realizo dicho parche, es decir en que lugar o parte

```bash
$ git commit -m 'Add component_name - component_type'
$ git commit -m 'Add library_name vX.X.X - componente_type'
$ git commit -m 'Del component_name - component_type, reason'
$ git commit -m 'Fix problem in somewhere_of_the_system'
```

Estandares para ramas:
----------------------
A continuacion veremos los estandares para crear ramas, y colaborar con el proyecto.

* Parches(Bug fixeds): las ramas para el desarrollo de algun parche, deben de tener la siguiente nomenclatura: **issue-#nro_de_ticket**. Ejm: issue-#699 


* Nuevas caracteristicas(features): las ramas para el desarrollo de alguna nueva caracteristica, deben de tener la siguiente nomenclatura: **feature-caracteristica** o **feature-#nro_de_propuesta**. Ejm: feature-new-report-admin o feature-#3


A tener en cuenta:
------------------
Información adicional a tener en cuenta para hacer push o pull request.

* Los **push** deben contener commits que esten relacionados al motivo del push.
* Los **pull request** deben hacerse usando la rama del parche o nueva caracteristica hacia la rama master del repositorio oficial
* Para desarrollar un parche o nueva caracteristica siempre se debe seguir los estandares para ramas
* Para realizar cualquier tipo de accion commit, push, merge o cualquier otro comando, no olvidar de leer antes el flujo de trabajo que se usa.


