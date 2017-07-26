guide
=====

1) Перейти в папку, в которой будет размещаться проект и выполнить команду:
git 

2) Перейти в папку с проектом и выполнить команду:
composer update

3) На этом этапе нужно указать ваши настройки подключения к БД в файле 

4) Выполнить команду на создание базы (если базы еще нет, если уже создана, то пропускаем):
php bin/console doctrine:schema:create 

5) Выполнить команду для создания таблиц
php bin/console doctrine:schema:update --force 

6) для ежедневной синхронизации курсов в полночь добавляем команду, поменяв при необходимости пути, в крон (crontab -e)
0 0 * * *  /usr/bin/php /var/www/curex/bin/console app:sync-rates  >>  /var/log/cron 2>&1

7) выполнить команду, если не хотим ждать синхронизации курсов по наступлению времени в кроне:
php bin/console app:sync-rates

8) запустить проект в браузере для вывода отчетов

* Комментарии по первому заданию - в src/AppBundle/Command/SyncRatesCommand
** Комментарии по второму заданию - в src/AppBundle/Repository/PaymentRepository
