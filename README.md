Интеграция с провайдером
========================

Используемые библиотеки
-----------------------
* [Sliex](http://silex.sensiolabs.org/) - микрофреймворк
* [OAuth 2.0 Server PHP](http://bshaffer.github.io/oauth2-server-php-docs/) - реализация OAuth2 сервера

Реализуемая логика
------------------
1. При связывании приложения с провайдером, пользователю показывается страница на сервере оператора, где ему предлагается ввести
свои аутентификационные данные
2. При успешной аутентификации, пользователю предлагается авторизовать приложение.
3. Если пользователь авторизует приложение, происходит переадресация на url-схему, заданную
в начале процесса. В запросе переадрисации указывается токен, с которым сервер приложения может обращаться к серверу оператора.
4. Приложение обращается к серверу провайдера с выданным токеном для получения информации о подписках.

Приложение не получает и не хранит аутентификационные данные пользователя у оператора, а сохраняет только токен.

Установка зависимостей
----------------------
`curl -sS https://getcomposer.org/installer | php`

`php composer.phar install`