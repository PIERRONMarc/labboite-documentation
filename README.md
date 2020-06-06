# LabBoîte documentation 

A website to list and present machines, tools and softwares available in LabBoîte, fablab of Cergy-Pontoise.

### Index

Softwares and hardware tools are organized in categories : users can quickly find the tool they looking for and the fablab can organized its tools according to their function. 

Cliking on a category display all to tools linked or lead directly to a tool page.

Admins are free to re-order the items and active or disable each element — if the tool hasn't a page.

Content of the index section is managed with a database.

### Tool page

The tools page gathers all the information to know about the tool.

User can find, dependly of the tool : 
* the tool specs
* a tutorial 
* the needed consumables
* a Q&A 
* some tips

Admins publish and update this part of the website. As the fablab has many different tools and each has its particularities, the content of the tools page adapts whether the sections are filled or not — e.g. tips section can be hidden if no tips were published.

## Database

For more details about database, see the conceptual data model at ./documentation/labboite-cdm.jpg

## Docker

This project is docker enabled, just `'docker-compose up'` and you're good to go !

## Things you should know

* This project use CKEditor 4 : https://ckeditor.com/docs/ckeditor4/latest/
* This project use elFinder to manage files in CKEditor : https://github.com/Studio-42/elFinder
* Each folder in the template directory can be divided in 2 folder : 
    - an "admin" folder : for back-office templates
    - a "public" folder : for front-office templates

## Stack

* Symfony v4.4.7
* MySQL v5.7
* Docker (-compose v3)

## Installation

1. Clone the projet at desired path
2. Install dependencies with composer
    >composer install
3. Edit the .env file with your database informations
4. Create and set up your database
    >php bin/console doctrine:database:create  
    php bin/console doctrine:migrations:migrate
5. Optionnal : load fake data
    >php bin/console doctrine:load:fixtures

## Credits

Design : Valentin Socha  
Development : [PIERRON Marc](https://marcpierron.com/), [HANNA Filoupatir](https://github.com/filou78955)
